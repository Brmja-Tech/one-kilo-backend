<?php

namespace App\Http\Controllers\Api\Payments;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\ApiController;
use App\Models\Payment;
use App\Services\Payments\Kashier\KashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KashierController extends ApiController
{
    public function __construct(protected KashierService $kashierService)
    {
    }

    public function callback(Request $request)
    {
        $payment = null;

        try {
            $payment = $this->kashierService->handleRedirect(
                $request->all(),
                $request->query()
            );
        } catch (\Throwable $e) {
            Log::channel((string) config('kashier.log_channel', 'stack'))
                ->error('Kashier callback handling failed', [
                    'message' => $e->getMessage(),
                    'query' => $request->query(),
                ]);
        }

        if ($payment) {
            $payment->loadMissing('order');
        }

        $state = 'pending';
        $title = __('front.payment-result-pending-title');
        $message = __('front.payment-result-pending-message');

        if (! $payment) {
            $state = 'failed';
            $title = __('front.payment-result-unverified-title');
            $message = __('front.payment-result-unverified-message');
        } elseif ($payment->status === Payment::STATUS_PAID) {
            $state = 'success';
            $title = __('front.payment-result-success-title');
            $message = __('front.payment-result-success-message');
        } elseif (in_array($payment->status, [Payment::STATUS_FAILED, Payment::STATUS_EXPIRED], true)) {
            $state = 'failed';
            $title = __('front.payment-result-failed-title');
            $message = __('front.payment-result-failed-message');
        } elseif ($payment->verified_at === null) {
            $state = 'pending';
            $title = __('front.payment-result-unverified-title');
            $message = __('front.payment-result-unverified-message');
        }

        $orderNumber = $payment?->order?->order_number;

        $redirectUrl = $this->buildCallbackRedirectUrl($state, $orderNumber);

        return response()->view('payments.kashier-result', [
            'state' => $state,
            'title' => $title,
            'message' => $message,
            'orderNumber' => $orderNumber,
            'redirectUrl' => $redirectUrl,
            'buttonText' => __('front.continue'),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function webhook(Request $request)
    {
        $expectedToken = (string) config('kashier.webhook_token');
        $receivedToken = (string) $request->query('token', '');

        if ($expectedToken !== '' && ! hash_equals($expectedToken, $receivedToken)) {
            Log::channel((string) config('kashier.webhook_log_channel', 'kashier_webhook'))
                ->warning('Kashier webhook token mismatch', [
                    'query' => $this->sanitizeWebhookQuery($request->query()),
                    'headers' => $this->sanitizeWebhookHeaders($request->headers->all()),
                ]);

            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        $query = $request->query();
        unset($query['token']);

        $this->kashierService->handleWebhook(
            $request->all(),
            $request->headers->all(),
            $query,
            $request->getContent()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.webhook-processed-successfully'),
            []
        );
    }

    protected function buildCallbackRedirectUrl(string $state, ?string $orderNumber): string
    {
        $base = (string) (config('kashier.callback_redirect_url') ?: config('app.url'));

        $params = [
            'status' => $state === 'success' ? 'true' : 'false',
        ];

        if ($orderNumber) {
            $params['order_number'] = $orderNumber;
        }

        if ($state === 'pending') {
            $params['pending'] = 'true';
        }

        return $this->appendQueryParams($base, $params);
    }

    protected function appendQueryParams(string $url, array $params): string
    {
        $params = array_filter($params, static fn ($value) => $value !== null && $value !== '');

        if ($params === []) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query($params);
    }

    protected function sanitizeWebhookQuery(array $query): array
    {
        if (array_key_exists('token', $query)) {
            $query['token'] = '***';
        }

        return $query;
    }

    protected function sanitizeWebhookHeaders(array $headers): array
    {
        $sanitized = [];

        foreach ($headers as $key => $values) {
            $normalizedKey = strtolower((string) $key);

            if (in_array($normalizedKey, ['authorization', 'cookie', 'x-api-key', 'api-key'], true)) {
                continue;
            }

            $sanitized[$normalizedKey] = $values;
        }

        return $sanitized;
    }
}
