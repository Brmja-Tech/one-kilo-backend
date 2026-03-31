<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappChannel
{
    /**
     * BULQ API settings
     */
    protected string $apiUrl = 'https://app.bulq.chat/api/v1/bluebird/messages/template';
    protected string $token  = 'c2b9346330b70290c6b390ca8ec73eb65a91533ce984b2628bb7172a2cd3702c';

    /**
     * Send OTP via BULQ template endpoint.
     *
     * Supported notification methods:
     * - toWhatsapp($notifiable)
     * - toBulq($notifiable)
     *
     * Expected payload from notification:
     * [
     *     'phone' => '201140158807', // required
     *     'code'  => '12345',        // required
     * ]
     */
    public function send($notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toBulq') && !method_exists($notification, 'toWhatsapp')) {
            return;
        }

        $message = method_exists($notification, 'toBulq')
            ? $notification->toBulq($notifiable)
            : $notification->toWhatsapp($notifiable);

        if (!is_array($message) || !isset($message['phone'], $message['code'])) {
            Log::error('Invalid WhatsApp OTP payload. Required: phone & code.', [
                'message' => $message,
            ]);
            return;
        }

        $phone = $this->normalizePhone($message['phone']);
        $code  = (string) $message['code'];

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'phone_number'      => $phone,
                    'template_name'     => 'login_otp',
                    'template_language' => 'ar',
                    'field_1'           => $code,
                ]);

            if ($response->failed()) {
                Log::error('❌ Failed sending OTP via BULQ', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'phone'  => $phone,
                ]);
                return;
            }

            Log::info('✅ OTP sent successfully via BULQ', [
                'status' => $response->status(),
                'phone'  => $phone,
                'body'   => $response->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('📛 BULQ OTP Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Normalize phone number for BULQ endpoint.
     * Converts to digits only and removes leading +
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^\d]/', '', $phone);

        return ltrim($phone, '+');
    }
}
