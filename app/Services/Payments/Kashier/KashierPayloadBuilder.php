<?php

namespace App\Services\Payments\Kashier;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Arr;

class KashierPayloadBuilder
{
    public function buildCreateSessionPayload(Order $order, Payment $payment): array
    {
        $order->loadMissing('user');

        $expiresAt = now()
            ->addMinutes(max((int) config('kashier.session_expire_minutes', 30), 1))
            ->toIso8601String();

        $merchantRedirectUrl = $this->appendQueryParams(
            $this->resolveUrl(
                (string) config('kashier.merchant_redirect_url'),
                route('api.payments.kashier.callback')
            ),
            [
                'payment_id' => $payment->id,
                'order_number' => $order->order_number,
            ]
        );

        $serverWebhookUrl = $this->appendQueryParams(
            $this->resolveUrl(
                (string) config('kashier.server_webhook_url'),
                route('api.payments.kashier.webhook')
            ),
            [
                'payment_id' => $payment->id,
                'order_number' => $order->order_number,
                'token' => config('kashier.webhook_token'),
            ]
        );

        $payload = [
            // Required (per Kashier Payment Sessions docs)
            'expireAt' => $expiresAt,
            'maxFailureAttempts' => (int) config('kashier.max_failure_attempts', 3),
            // Kashier requires "amount" to be a string (e.g. "188.89").
            'amount' => (string) $payment->amount,
            'currency' => (string) config('kashier.currency', 'EGP'),
            'order' => (string) $payment->merchant_order_id,
            'merchantId' => (string) config('kashier.merchant_id'),
            'merchantRedirect' => $merchantRedirectUrl,
            'type' => (string) config('kashier.type', 'external'),
            'paymentType' => 'credit',
            'allowedMethods' => 'card,wallet',
            'enable3DS' => true,
            'interactionSource' => 'ECOMMERCE',

            // Minimal optional fields kept in the stable integration
            'serverWebhook' => $serverWebhookUrl,
            'customer' => $this->buildCustomer($order, $payment),
            'metaData' => [
                'payment_id' => (string) $payment->id,
                'order_id' => (string) $order->id,
                'order_number' => (string) $order->order_number,
                'user_id' => (string) $order->user_id,
            ],
            'description' => 'Order ' . $order->order_number,
            'display' => $this->resolveDisplay(),
        ];

        $payload = $this->removeNullValues($payload);

        $this->assertValidUrl((string) data_get($payload, 'merchantRedirect'), 'merchantRedirect');
        if (data_get($payload, 'serverWebhook')) {
            $this->assertValidUrl((string) data_get($payload, 'serverWebhook'), 'serverWebhook');
        }

        return $payload;
    }

    protected function resolveMode(): string
    {
        $mode = strtolower(trim((string) config('kashier.mode', 'test')));

        return in_array($mode, ['test', 'live'], true) ? $mode : 'test';
    }

    protected function resolveDisplay(): ?string
    {
        $display = trim((string) config('kashier.display', ''));

        return $display !== '' ? $display : null;
    }

    protected function buildCustomer(Order $order, Payment $payment): array
    {
        $customer = [
            // Kashier requires customer.reference (string).
            'reference' => $this->buildCustomerReference($order, $payment),
        ];

        if ($order->relationLoaded('user') && $order->user) {
            $customer += [
                'name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ];
        }

        $customer = array_filter($customer, static fn($value) => $value !== null && $value !== '');

        return $customer;
    }

    protected function buildCustomerReference(Order $order, Payment $payment): string
    {
        return (string) ($order->order_number . '-P' . $payment->id);
    }

    protected function removeNullValues(array $payload): array
    {
        $payload = Arr::map($payload, function ($value) {
            if (is_array($value)) {
                return $this->removeNullValues($value);
            }

            return $value;
        });

        return array_filter($payload, static function ($value) {
            if ($value === null) {
                return false;
            }

            if (is_array($value)) {
                return $value !== [];
            }

            if (is_string($value)) {
                return trim($value) !== '';
            }

            return true;
        });
    }

    protected function resolveUrl(string $configuredValue, string $fallback): string
    {
        $configuredValue = trim($configuredValue);

        if ($configuredValue === '') {
            return $fallback;
        }

        // Support env values like: route('api.payments.kashier.callback') (optionally with query string)
        if (preg_match('/^route\\([\'"]([^\'"]+)[\'"]\\)(\\?.*)?$/', $configuredValue, $matches) === 1) {
            try {
                $url = route($matches[1]);

                if (! empty($matches[2])) {
                    $url .= $matches[2];
                }

                return $url;
            } catch (\Throwable) {
                return $fallback;
            }
        }

        // Support env values like: api.payments.kashier.callback (route name)
        if (! str_contains($configuredValue, '://') && preg_match('/^[A-Za-z0-9_.-]+$/', $configuredValue) === 1) {
            try {
                return route($configuredValue);
            } catch (\Throwable) {
                // continue to return as-is
            }
        }

        // Support relative URLs (e.g. /payment/callback)
        if (str_starts_with($configuredValue, '/')) {
            return rtrim((string) config('app.url'), '/') . $configuredValue;
        }

        return $configuredValue;
    }

    protected function appendQueryParams(string $url, array $params): string
    {
        $params = array_filter($params, static fn($value) => $value !== null && $value !== '');

        if ($params === []) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query($params);
    }

    protected function assertValidUrl(string $url, string $field): void
    {
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        throw new KashierException('Invalid Kashier URL for ' . $field, [
            'field' => $field,
            'url' => $url,
        ]);
    }
}
