<?php

namespace App\Services\Payments\Kashier;

use App\Exceptions\ApiBusinessException;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Api\Payments\PaymentRepository;
use Illuminate\Support\Facades\Log;

class KashierService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected KashierSessionService $sessionService,
        protected KashierPaymentStatusService $paymentStatusService,
        protected KashierWebhookService $webhookService
    ) {
    }

    public function createCheckoutSession(Order $order): Payment
    {
        if (! config('kashier.enabled')) {
            throw new ApiBusinessException(
                __('front.payment-gateway-unavailable'),
                422,
                ['payment_method' => [__('front.payment-gateway-unavailable')]]
            );
        }

        $payment = $this->paymentRepository->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'gateway' => Payment::GATEWAY_KASHIER,
            'merchant_order_id' => $order->order_number,
            'amount' => round((float) $order->total, 2),
            'currency' => (string) config('kashier.currency', 'EGP'),
            'status' => Payment::STATUS_PENDING,
            'meta' => [
                'source' => 'checkout',
            ],
        ]);

        $payment = $this->sessionService->createCheckoutSession($order, $payment);

        // Lightweight trace in order logs, without changing status.
        $order->statusLogs()->create([
            'old_status' => $order->status,
            'new_status' => $order->status,
            'title' => __('dashboard.payment-session-created'),
            'description' => __('dashboard.payment-session-created-description', [
                'gateway' => 'Kashier',
                'session' => $payment->session_id ?: '-',
            ]),
            'meta' => [
                'source' => 'kashier',
                'event' => 'session_created',
                'payment_id' => $payment->id,
                'session_id' => $payment->session_id,
            ],
        ]);

        return $payment;
    }

    public function handleRedirect(array $payload, array $query = []): Payment
    {
        $payment = $this->resolvePaymentFromCallback($payload, $query);

        if (! $payment) {
            throw new ApiBusinessException(
                __('validation.resource-not-found'),
                404,
                []
            );
        }

        // Store raw callback payload FIRST for auditing, even if reconciliation fails later.
        $payment->update([
            'callback_payload' => [
                'body' => $payload,
                'query' => $query,
            ],
        ]);

        Log::channel((string) config('kashier.log_channel', 'stack'))
            ->info('Kashier callback stored, reconciling...', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'session_id' => $payment->session_id,
            ]);

        try {
            return $this->paymentStatusService->reconcile($payment);
        } catch (\Throwable $e) {
            Log::channel((string) config('kashier.log_channel', 'stack'))
                ->error('Kashier callback reconcile failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'session_id' => $payment->session_id,
                    'message' => $e->getMessage(),
                ]);

            return $payment->fresh(['order']);
        }
    }

    public function handleWebhook(array $payload, array $headers = [], array $query = [], ?string $rawBody = null): ?Payment
    {
        return $this->webhookService->handle($payload, $headers, $query, $rawBody);
    }

    public function refreshPaymentStatus(Payment $payment): Payment
    {
        return $this->paymentStatusService->reconcile($payment);
    }

    protected function resolvePaymentFromCallback(array $payload, array $query = []): ?Payment
    {
        $paymentId = $query['payment_id'] ?? $payload['payment_id'] ?? null;

        if (is_numeric($paymentId)) {
            $payment = $this->paymentRepository->findById((int) $paymentId);
            if ($payment) {
                return $payment;
            }
        }

        $sessionId = (string) (
            $query['sessionId'] ?? $query['session_id'] ?? null
                ?? data_get($payload, 'sessionId')
                ?? data_get($payload, 'session_id')
                ?? data_get($payload, 'data.sessionId')
                ?? ''
        );

        if ($sessionId !== '') {
            $payment = $this->paymentRepository->findBySessionId($sessionId);
            if ($payment) {
                return $payment;
            }
        }

        $merchantOrderId = (string) (
            $query['orderId'] ?? $query['order_id'] ?? null
                ?? data_get($payload, 'orderId')
                ?? data_get($payload, 'order_id')
                ?? data_get($payload, 'data.orderId')
                ?? ''
        );

        if ($merchantOrderId !== '') {
            return $this->paymentRepository->findByMerchantOrderId($merchantOrderId);
        }

        return null;
    }
}
