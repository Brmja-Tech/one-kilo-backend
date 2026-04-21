<?php

namespace App\Services\Api\Commerce;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Psr\Log\LoggerInterface;

class FirebaseService
{
    protected \Kreait\Firebase\Messaging $messaging;
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        // Load the service account JSON from config
        $credentialsPath = config('services.firebase.credentials');

        $factory = (new Factory)
            ->withServiceAccount($credentialsPath);

        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send an FCM push notification to a single device.
     *
     * @param  string  $deviceToken
     * @param  string  $title
     * @param  string  $body
     * @return bool
     */
    public function sendNotification(string $deviceToken, string $title, string $body): bool
    {
        $notification = FirebaseNotification::create($title, $body);

        $androidConfig = AndroidConfig::fromArray([
            'priority'     => 'high',
            'notification' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);

        $apnsConfig = ApnsConfig::fromArray([
            'headers' => ['apns-priority' => '10'],
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                ],
            ],
        ]);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withAndroidConfig($androidConfig)
            ->withApnsConfig($apnsConfig);

        try {
            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Firebase push to device failed', [
                'device_token' => $deviceToken,
                'title'        => $title,
                'body'         => $body,
                'error'        => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send an FCM push notification to the 'besohola' topic.
     *
     * @param  string  $title
     * @param  string  $body
     * @return bool
     */
    public function sendToTopic(string $title, string $body): bool
    {
        $topic = 'oneKilo';

        $notification = FirebaseNotification::create($title, $body);

        $androidConfig = AndroidConfig::fromArray([
            'priority'     => 'high',
            'notification' => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);

        $apnsConfig = ApnsConfig::fromArray([
            'headers' => ['apns-priority' => '10'],
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                ],
            ],
        ]);

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification)
            ->withAndroidConfig($androidConfig)
            ->withApnsConfig($apnsConfig);

        try {
            $this->messaging->send($message);
            Log::info('Firebase push to topic succeeded', [
                'topic' => $topic,
                'title' => $title,
                'body'  => $body,
            ]);
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Firebase push to topic failed', [
                'topic' => $topic,
                'title' => $title,
                'body'  => $body,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }




    public function saveNotification($notifiable, $orderId, $title, $msg)
        {

            return $notifiable->appNotifications()->create([
                'order_id' => $orderId,
                'title' => $title,
                'message' => $msg,
            ]);
        }


    public function saveAllNotifications($title, $msg)
    {
        $users = User::where('status', 1)->get();

        foreach ($users as $user) {
            $user->appNotifications()->create([
                'title' => $title,
                'message' => $msg,
            ]);
        }
    }


}
