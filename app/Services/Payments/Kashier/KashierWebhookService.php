<?php

namespace App\Services\Payments\Kashier;

use App\Models\Payment;
use App\Repositories\Api\Payments\PaymentRepository;
use Illuminate\Support\Facades\Log;

class KashierWebhookService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected KashierPaymentStatusService $paymentStatusService
    ) {
    }

    public function handle(array $payload, array $headers = [], array $query = [], ?string $rawBody = null): ?Payment
    {
        $webhookLogChannel = (string) config('kashier.webhook_log_channel', config('kashier.log_channel', 'stack'));
        $safeQuery = $this->sanitizeQuery($query);

        $payment = $this->resolvePayment($payload, $query);

        if (! $payment) {
            Log::channel($webhookLogChannel)
                ->warning('Kashier webhook received for unknown payment', [
                    'payload' => $payload,
                    'query' => $safeQuery,
                    'headers' => $this->sanitizeHeaders($headers),
                ]);

            return null;
        }

        // Store raw webhook payload FIRST for auditing, even if reconciliation fails later.
        $payment->update([
            'webhook_payload' => [
                'body' => $payload,
                'raw' => $rawBody,
                'query' => $safeQuery,
                'headers' => $this->sanitizeHeaders($headers),
            ],
        ]);

        Log::channel($webhookLogChannel)
            ->info('Kashier webhook stored, reconciling...', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'session_id' => $payment->session_id,
            ]);

        try {
            return $this->paymentStatusService->reconcile($payment);
        } catch (\Throwable $e) {
            Log::channel($webhookLogChannel)
                ->error('Kashier webhook reconcile failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'session_id' => $payment->session_id,
                    'message' => $e->getMessage(),
                ]);

            throw $e;
        }
    }

    protected function resolvePayment(array $payload, array $query = []): ?Payment
    {
        $paymentId = $query['payment_id'] ?? null;
        if (is_numeric($paymentId)) {
            $payment = $this->paymentRepository->findById((int) $paymentId);
            if ($payment) {
                return $payment;
            }
        }

        $sessionId = (string) (
            data_get($payload, 'sessionId')
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
            data_get($payload, 'orderId')
                ?? data_get($payload, 'order_id')
                ?? data_get($payload, 'data.orderId')
                ?? ''
        );

        if ($merchantOrderId !== '') {
            return $this->paymentRepository->findByMerchantOrderId($merchantOrderId);
        }

        return null;
    }

    protected function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];

        foreach ($headers as $key => $values) {
            $normalizedKey = strtolower((string) $key);

            if (in_array($normalizedKey, ['authorization', 'cookie', 'x-api-key', 'api-key'], true)) {
                continue;
            }

            if (is_array($values)) {
                $value = implode(', ', array_map(static fn ($v) => (string) $v, $values));
            } else {
                $value = (string) $values;
            }

            $sanitized[$normalizedKey] = mb_substr($value, 0, 2000);
        }

        return $sanitized;
    }

    protected function sanitizeQuery(array $query): array
    {
        if (array_key_exists('token', $query)) {
            $query['token'] = '***';
        }

        return $query;
    }
}
