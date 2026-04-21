<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AdminNotification extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'message',
    ];

    public $translatable = [
        'title',
        'message',
    ];
}
