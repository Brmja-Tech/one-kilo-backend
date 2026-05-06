<?php

namespace App\Services\Payments\Kashier;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class KashierSessionService
{
    public function __construct(
        protected KashierHttpClient $httpClient,
        protected KashierPayloadBuilder $payloadBuilder
    ) {
    }

    public function createCheckoutSession(Order $order, Payment $payment): Payment
    {
        $payload = $this->payloadBuilder->buildCreateSessionPayload($order, $payment);
        $safePayload = $this->sanitizePayloadForStorage($payload);

        Log::channel((string) config('kashier.log_channel', 'stack'))
            ->info('Kashier create session request', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_id' => $payment->id,
                'merchant_order_id' => $payment->merchant_order_id,
                'payload' => $safePayload,
            ]);

        $response = $this->httpClient->createPaymentSession($payload);

        $sessionId = $this->extractSessionId($response);
        $sessionUrl = $this->extractSessionUrl($response);

        if (! $sessionUrl) {
            Log::channel((string) config('kashier.log_channel', 'stack'))
                ->error('Kashier create session response missing sessionUrl', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'response' => $response,
                ]);

            throw new KashierException('Kashier sessionUrl is missing in response', [
                'response' => $response,
            ]);
        }

        $payment->update([
            'session_id' => $sessionId,
            'payment_url' => $sessionUrl,
            'request_payload' => $safePayload,
            'create_session_response' => $response,
        ]);

        Log::channel((string) config('kashier.log_channel', 'stack'))
            ->info('Kashier create session success', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'session_id' => $sessionId,
            ]);

        return $payment;
    }

    protected function sanitizePayloadForStorage(array $payload): array
    {
        $payload = $this->sanitizeUrlInPayload($payload, 'serverWebhook', 'token');

        return $payload;
    }

    protected function sanitizeUrlInPayload(array $payload, string $key, string $queryParam): array
    {
        if (! isset($payload[$key]) || ! is_string($payload[$key]) || $payload[$key] === '') {
            return $payload;
        }

        $payload[$key] = $this->removeQueryParamFromUrl($payload[$key], $queryParam);

        return $payload;
    }

    protected function removeQueryParamFromUrl(string $url, string $param): string
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return $url;
        }

        $query = [];
        parse_str($parts['query'] ?? '', $query);

        if (! array_key_exists($param, $query)) {
            return $url;
        }

        unset($query[$param]);

        $rebuiltQuery = http_build_query($query);

        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $user = $parts['user'] ?? null;
        $pass = $parts['pass'] ?? null;
        $auth = $user ? $user . ($pass ? ':' . $pass : '') . '@' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        $queryString = $rebuiltQuery !== '' ? '?' . $rebuiltQuery : '';

        return $scheme . $auth . $host . $port . $path . $queryString . $fragment;
    }

    protected function extractSessionId(array $response): ?string
    {
        $candidates = [
            data_get($response, '_id'),
            data_get($response, 'sessionId'),
            data_get($response, 'session.id'),
            data_get($response, 'data.sessionId'),
            data_get($response, 'data.session.id'),
            data_get($response, 'id'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    protected function extractSessionUrl(array $response): ?string
    {
        $candidates = [
            data_get($response, 'sessionUrl'),
            data_get($response, 'session.sessionUrl'),
            data_get($response, 'data.sessionUrl'),
            data_get($response, 'data.session.sessionUrl'),
            data_get($response, 'url'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
