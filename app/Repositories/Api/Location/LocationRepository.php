<?php

namespace App\Repositories\Api\Location;

use App\Models\Country;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository
{
    public function getActiveCountriesWithGovernorates(): Collection
    {
        return Country::query()
            ->where('status', 1)
            ->orderBy('id')
            ->with(['governorates' => function ($q) {
                $q->where('status', 1)->orderBy('id');
            }])
            ->get();
    }

    public function getActiveGovernoratesByCountry(int $countryId): Collection
    {
        return Governorate::query()
            ->where('country_id', $countryId)
            ->where('status', 1)
            ->whereHas('country', fn ($query) => $query->where('status', 1))
            ->orderBy('id')
            ->get();
    }

    public function getActiveRegionsByGovernorate(int $governorateId): Collection
    {
        return Region::query()
            ->where('governorate_id', $governorateId)
            ->where('status', 1)
            ->whereHas('governorate', fn ($query) => $query
                ->where('status', 1)
                ->whereHas('country', fn ($countryQuery) => $countryQuery->where('status', 1)))
            ->orderBy('id')
            ->get();
    }
}
