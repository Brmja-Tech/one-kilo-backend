<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'contact_name',
        'phone',
        'country_id',
        'governorate_id',
        'region_id',
        'city',
        'area',
        'street',
        'building_number',
        'floor',
        'apartment_number',
        'landmark',
        'latitude',
        'longitude',
        'is_default',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->street,
            $this->building_number ? 'Building ' . $this->building_number : null,
            $this->floor ? 'Floor ' . $this->floor : null,
            $this->apartment_number ? 'Apt ' . $this->apartment_number : null,
            $this->area,
            $this->city,
            $this->region?->name,
            $this->governorate?->name,
            $this->country?->name,
            $this->landmark ? 'Landmark: ' . $this->landmark : null,
        ], fn ($value) => filled($value));

        return implode(', ', $parts);
    }
}
