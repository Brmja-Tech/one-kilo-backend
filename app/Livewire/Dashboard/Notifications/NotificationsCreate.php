<?php

namespace App\Livewire\Dashboard\Notifications;

use App\Livewire\Dashboard\Notifications\NotificationsData;
use App\Models\AdminNotification;
use App\Models\Faq;
use App\Models\Notification;
use App\Services\Api\Order\FirebaseService;
use App\Services\Dashboard\OrderService;
use Livewire\Component;
use CodeZero\UniqueTranslation\UniqueTranslationRule;

class NotificationsCreate extends Component
{
    public $title_ar, $title_en, $message_ar, $message_en;

    public function rules()
    {
        return [
            'title_ar' => [
                'required',
                'string',
                'min:4',
                'max:200',
                UniqueTranslationRule::for('notifications', 'title')
            ],
            'title_en' => [
                'required',
                'string',
                'min:4',
                'max:200',
                UniqueTranslationRule::for('notifications', 'title')
            ],

            'message_ar'          => 'required|string|min:4|',
            'message_en'          => 'required|string|min:4|',
        ];
    }

    public function submit(FirebaseService $firebaseService)
    {
        $data = $this->validate();
        // save data in DB
        $data['title']  = [
            'ar' => $this->title_ar,
            'en' => $this->title_en,
        ];
        $data['message']   = [
            'ar' => $this->message_ar,
            'en' => $this->message_en,
        ];
        $admin_notifications = Notification::create($data);


        //send firebase notifications
        $firebaseService->sendToTopic($data['title']['ar'],$data['message']['ar']);


        $this->reset('title_ar', 'title_en', 'message_ar', 'message_en');
        $this->dispatch('notificationAddMS');
        //hide modal
        $this->dispatch('createModalToggle');
        //refresh blog data in component
        $this->dispatch('refreshData')->to(NotificationsData::class);
    }
    public function render()
    {
        return view('dashboard.notifications.notifications-create');
    }
}
