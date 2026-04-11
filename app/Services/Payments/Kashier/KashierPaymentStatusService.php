<?php

namespace App\Services\Payments\Kashier;

use App\Services\Api\Commerce\CardPaymentFinalizationService;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Api\Commerce\CouponRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KashierPaymentStatusService
{
    public function __construct(
        protected KashierHttpClient $httpClient,
        protected CouponRepository $couponRepository,
        protected CardPaymentFinalizationService $cardPaymentFinalizationService
    ) {
    }

    public function reconcile(Payment $payment): Payment
    {
        if (! $payment->session_id) {
            throw new KashierException('Payment session_id is missing', [
                'payment_id' => $payment->id,
            ]);
        }

        $response = $this->httpClient->getSessionPayment($payment->session_id);

        Log::channel((string) config('kashier.log_channel', 'stack'))
            ->info('Kashier reconcile response', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'session_id' => $payment->session_id,
                'response' => $response,
            ]);

        return $this->applyGatewayStatus($payment, $response);
    }

    protected function applyGatewayStatus(Payment $payment, array $gatewayPayload): Payment
    {
        $normalized = $this->normalizeGatewayPayload($gatewayPayload);
        $now = now();

        return DB::transaction(function () use ($payment, $gatewayPayload, $normalized, $now) {
            $lockedPayment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedOrder = Order::query()
                ->whereKey($lockedPayment->order_id)
                ->lockForUpdate()
                ->firstOrFail();

            $paymentUpdate = [
                'gateway_status' => $normalized['gateway_status'] ?? $lockedPayment->gateway_status,
                'transaction_id' => $normalized['transaction_id'] ?: $lockedPayment->transaction_id,
                'reference' => $normalized['reference'] ?: $lockedPayment->reference,
                'payment_method' => $normalized['payment_method'] ?: $lockedPayment->payment_method,
                'verified_at' => $now,
                'reconcile_payload' => $gatewayPayload,
            ];

            $targetStatus = $normalized['status'];

            if ($lockedPayment->status === Payment::STATUS_PAID && $targetStatus !== Payment::STATUS_REFUNDED) {
                $targetStatus = Payment::STATUS_PAID;
            }

            if ($lockedPayment->status === Payment::STATUS_REFUNDED) {
                $targetStatus = Payment::STATUS_REFUNDED;
            }

            if (in_array($lockedPayment->status, [Payment::STATUS_FAILED, Payment::STATUS_EXPIRED], true)) {
                $targetStatus = $targetStatus === Payment::STATUS_PAID
                    ? Payment::STATUS_PAID
                    : $lockedPayment->status;
            }

            if ($targetStatus !== $lockedPayment->status) {
                $paymentUpdate['status'] = $targetStatus;

                if ($targetStatus === Payment::STATUS_PAID) {
                    $paymentUpdate['paid_at'] = $lockedPayment->paid_at ?: $now;
                    $paymentUpdate['failed_at'] = null;
                } elseif (in_array($targetStatus, [Payment::STATUS_FAILED, Payment::STATUS_EXPIRED], true)) {
                    $paymentUpdate['failed_at'] = $lockedPayment->failed_at ?: $now;
                }
            }

            $lockedPayment->update($paymentUpdate);

            $this->applyOrderUpdates($lockedOrder, $lockedPayment, $targetStatus, $now);

            return $lockedPayment->fresh(['order']);
        });
    }

    protected function applyOrderUpdates(Order $order, Payment $payment, string $paymentStatus, $now): void
    {
        if ($paymentStatus === Payment::STATUS_PAID) {
            $this->markOrderPaid($order, $payment, $now);
            return;
        }

        if ($paymentStatus === Payment::STATUS_REFUNDED) {
            $this->markOrderRefunded($order);
            return;
        }

        if (in_array($paymentStatus, [Payment::STATUS_FAILED, Payment::STATUS_EXPIRED], true)) {
            $this->markOrderFailed($order, $paymentStatus);
        }
    }

    protected function markOrderPaid(Order $order, Payment $payment, $now): void
    {
        $oldStatus = $order->status;

        $data = [];

        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            $data['payment_status'] = Order::PAYMENT_STATUS_PAID;
            $data['paid_at'] = $order->paid_at ?: $now;
        }

        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_AWAITING_PAYMENT, Order::STATUS_FAILED], true)) {
            $data['status'] = Order::STATUS_CONFIRMED;
        }

        if ($data !== []) {
            $order->update($data);
        }

        if ($oldStatus !== $order->status) {
            $order->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $order->status,
                'title' => __('dashboard.order-status-updated'),
                'description' => __('dashboard.order-status-changed-by-payment-description', [
                    'from' => __('dashboard.order-status-' . str_replace('_', '-', $oldStatus)),
                    'to' => __('dashboard.order-status-' . str_replace('_', '-', $order->status)),
                ]),
                'meta' => [
                    'source' => 'kashier',
                    'payment_id' => $payment->id,
                ],
            ]);
        }

        $this->consumeCouponIfNeeded($order);

        try {
            $this->cardPaymentFinalizationService->finalizeSuccessfulCardPayment($order, $payment);
        } catch (\Throwable $e) {
            // We never want to lose the "paid" status update because stock/cart finalization failed.
            // This should be safe to retry on subsequent callbacks/webhooks/reconciliations.
            Log::channel((string) config('kashier.log_channel', 'stack'))
                ->error('Card payment finalization failed after reconciliation', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'message' => $e->getMessage(),
                ]);
        }
    }

    protected function markOrderFailed(Order $order, string $failureType): void
    {
        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return;
        }

        $oldStatus = $order->status;

        $data = [
            'payment_status' => Order::PAYMENT_STATUS_FAILED,
        ];

        // In One Kilo, card orders are created as "awaiting_payment".
        // On a final gateway failure/expiry, we mark the order as "failed" to avoid leaving it hanging.
        if ($order->status === Order::STATUS_AWAITING_PAYMENT) {
            $data['status'] = Order::STATUS_FAILED;
        }

        $order->update($data);

        if ($oldStatus !== $order->status) {
            $order->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $order->status,
                'title' => __('dashboard.order-status-updated'),
                'description' => __('dashboard.order-status-changed-by-payment-description', [
                    'from' => __('dashboard.order-status-' . str_replace('_', '-', $oldStatus)),
                    'to' => __('dashboard.order-status-' . str_replace('_', '-', $order->status)),
                ]),
                'meta' => [
                    'source' => 'kashier',
                    'failure_type' => $failureType,
                ],
            ]);
        }
    }

    protected function markOrderRefunded(Order $order): void
    {
        if ($order->payment_status === Order::PAYMENT_STATUS_REFUNDED) {
            return;
        }

        $order->update([
            'payment_status' => Order::PAYMENT_STATUS_REFUNDED,
        ]);
    }

    protected function consumeCouponIfNeeded(Order $order): void
    {
        if (! $order->coupon_id) {
            return;
        }

        if (CouponUsage::query()->where('order_id', $order->id)->exists()) {
            return;
        }

        $coupon = $this->couponRepository->lockById((int) $order->coupon_id);

        if (! $coupon) {
            return;
        }

        $this->couponRepository->registerUsage(
            $coupon,
            (int) $order->user_id,
            (int) $order->id,
            round((float) $order->discount_amount, 2)
        );
    }

    protected function normalizeGatewayPayload(array $payload): array
    {
        $gatewayStatus = (string) (
            data_get($payload, 'status')
                ?? data_get($payload, 'payment.status')
                ?? data_get($payload, 'data.status')
                ?? data_get($payload, 'paymentStatus')
                ?? data_get($payload, 'data.paymentStatus')
                ?? ''
        );

        $status = $this->mapGatewayStatusToInternal($gatewayStatus);

        $transactionId = data_get($payload, 'transactionId')
            ?? data_get($payload, 'transaction_id')
            ?? data_get($payload, 'payment.transactionId')
            ?? data_get($payload, 'data.transactionId')
            ?? null;

        $reference = data_get($payload, 'reference')
            ?? data_get($payload, 'payment.reference')
            ?? data_get($payload, 'data.reference')
            ?? null;

        $paymentMethod = data_get($payload, 'method')
            ?? data_get($payload, 'paymentMethod')
            ?? data_get($payload, 'payment.method')
            ?? data_get($payload, 'data.method')
            ?? null;

        return [
            'status' => $status,
            'gateway_status' => $gatewayStatus !== '' ? $gatewayStatus : null,
            'transaction_id' => is_string($transactionId) ? $transactionId : null,
            'reference' => is_string($reference) ? $reference : null,
            'payment_method' => is_string($paymentMethod) ? $paymentMethod : null,
        ];
    }

    protected function mapGatewayStatusToInternal(string $gatewayStatus): string
    {
        $value = strtolower(trim($gatewayStatus));

        if ($value === '') {
            return Payment::STATUS_PENDING;
        }

        if (in_array($value, ['paid', 'success', 'successful', 'captured', 'completed', 'approved'], true)) {
            return Payment::STATUS_PAID;
        }

        if (in_array($value, ['refunded', 'refund', 'reversed', 'chargeback'], true)) {
            return Payment::STATUS_REFUNDED;
        }

        if (in_array($value, ['expired', 'timeout'], true)) {
            return Payment::STATUS_EXPIRED;
        }

        if (in_array($value, ['failed', 'failure', 'declined', 'rejected', 'canceled', 'cancelled', 'error'], true)) {
            return Payment::STATUS_FAILED;
        }

        return Payment::STATUS_PENDING;
    }
}
