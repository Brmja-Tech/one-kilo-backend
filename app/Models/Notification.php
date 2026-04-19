<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasTranslations;

    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'order_id',
        'title',
        'message'
    ];

    public $translatable = ['title', 'message'];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function appNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }


}
