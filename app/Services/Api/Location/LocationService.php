<?php

namespace App\Services\Api\Location;

use App\Repositories\Api\Location\LocationRepository;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    public function __construct(protected LocationRepository $repo)
    {
    }

    public function activeCountriesWithGovernorates(): Collection
    {
        return $this->repo->getActiveCountriesWithGovernorates();
    }

    public function activeGovernoratesByCountry(int $countryId): Collection
    {
        return $this->repo->getActiveGovernoratesByCountry($countryId);
    }

    public function activeRegionsByGovernorate(int $governorateId): Collection
    {
        return $this->repo->getActiveRegionsByGovernorate($governorateId);
    }
}
