<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\GovernorateResource;
use App\Http\Resources\RegionResource;
use App\Services\Api\Location\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(protected LocationService $service)
    {
    }

    /**
     * GET /api/countries
     * Returns active countries with their active governorates.
     */
    public function countries(Request $request)
    {
        $items = $this->service->activeCountriesWithGovernorates();

        return ApiResponse::sendResponse(
            200,
            __('front.countries-retrieved-successfully'),
            CountryResource::collection($items)
        );
    }

    /**
     * GET /api/countries/{country_id}/governorates
     * Returns active governorates for a country.
     */
    public function governorates(Request $request, int $country_id)
    {
        $items = $this->service->activeGovernoratesByCountry($country_id);

        return ApiResponse::sendResponse(
            200,
            __('front.governorates-retrieved-successfully'),
            GovernorateResource::collection($items)
        );
    }

    /**
     * GET /api/governorates/{governorate_id}/regions
     * Returns active regions for a governorate.
     */
    public function regions(Request $request, int $governorate_id)
    {
        $items = $this->service->activeRegionsByGovernorate($governorate_id);

        return ApiResponse::sendResponse(
            200,
            __('front.regions-retrieved-successfully'),
            RegionResource::collection($items)
        );
    }
}
