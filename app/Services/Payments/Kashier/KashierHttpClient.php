<?php

namespace App\Services\Payments\Kashier;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KashierHttpClient
{
    public function createPaymentSession(array $payload): array
    {
        $this->assertCreateSessionConfigured();

        $url = rtrim($this->baseUrl(), '/') . '/v3/payment/sessions';

        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => (string) config('kashier.secret_key'),
                    'api-key' => (string) config('kashier.api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);
        } catch (ConnectionException $e) {
            $this->logError('Kashier create session connection error', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            throw new KashierException('Kashier connection error', [], 0, $e);
        }

        if ($response->failed()) {
            $this->logError('Kashier create session failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new KashierException('Kashier create session failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        }

        return (array) ($response->json() ?? []);
    }

    public function getSessionPayment(string $sessionId): array
    {
        $this->assertGetSessionConfigured();

        $url = rtrim($this->baseUrl(), '/') . '/v3/payment/sessions/' . urlencode($sessionId) . '/payment';

        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->withHeaders([
                    'Authorization' => (string) config('kashier.secret_key'),
                ])
                ->get($url);
        } catch (ConnectionException $e) {
            $this->logError('Kashier get session payment connection error', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            throw new KashierException('Kashier connection error', [], 0, $e);
        }

        if ($response->failed()) {
            $this->logError('Kashier get session payment failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new KashierException('Kashier get session payment failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        }

        return (array) ($response->json() ?? []);
    }

    protected function baseUrl(): string
    {
        $mode = (string) config('kashier.mode', 'test');
        $baseUrls = (array) config('kashier.base_urls', []);

        if (! in_array($mode, ['test', 'live'], true)) {
            $mode = 'test';
        }

        $baseUrl = (string) ($baseUrls[$mode] ?? '');

        if ($baseUrl === '') {
            throw new KashierException('Kashier base URL is not configured', ['mode' => $mode]);
        }

        return $baseUrl;
    }

    protected function timeout(): int
    {
        return max((int) config('kashier.timeout', 30), 1);
    }

    protected function assertCreateSessionConfigured(): void
    {
        if (! config('kashier.enabled')) {
            throw new KashierException('Kashier is disabled');
        }

        $missing = [];

        if (! config('kashier.merchant_id')) {
            $missing[] = 'KASHIER_MERCHANT_ID';
        }
        if (! config('kashier.api_key')) {
            $missing[] = 'KASHIER_API_KEY';
        }
        if (! config('kashier.secret_key')) {
            $missing[] = 'KASHIER_SECRET_KEY';
        }

        if ($missing !== []) {
            throw new KashierException('Kashier credentials are missing', ['missing' => $missing]);
        }
    }

    protected function assertGetSessionConfigured(): void
    {
        if (! config('kashier.enabled')) {
            throw new KashierException('Kashier is disabled');
        }

        if (! config('kashier.secret_key')) {
            throw new KashierException('Kashier secret key is missing', ['missing' => ['KASHIER_SECRET_KEY']]);
        }
    }

    protected function logError(string $message, array $context = []): void
    {
        Log::channel((string) config('kashier.log_channel', 'stack'))
            ->error($message, $context);
    }
}

