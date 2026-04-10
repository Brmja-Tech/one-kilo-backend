<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }

    public function regions(): HasManyThrough
    {
        return $this->hasManyThrough(Region::class, Governorate::class, 'country_id', 'governorate_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
