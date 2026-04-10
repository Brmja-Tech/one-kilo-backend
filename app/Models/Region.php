<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'governorate_id',
        'name',
        'shipping_price',
        'status',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
