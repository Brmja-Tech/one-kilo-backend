<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class WorkingHour extends Model
{
    use HasTranslations;

    public $translatable = [
        'day_name',
    ];

    protected $fillable = [
        'day_of_week',
        'day_name',
        'open_time',
        'close_time',
        'status',
    ];

}
