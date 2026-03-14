<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Requests\Api\Commerce\DeleteAddressRequest;
use App\Http\Requests\Api\Commerce\SetDefaultAddressRequest;
use App\Http\Requests\Api\Commerce\StoreAddressRequest;
use App\Http\Requests\Api\Commerce\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\Api\Commerce\AddressService;

class AddressController extends ApiController
{
    public function __construct(protected AddressService $service) {}

    public function index()
    {
        $addresses = $this->service->list(auth('sanctum')->user()->id);

        return ApiResponse::sendResponse(
            200,
            __('front.addresses-retrieved-successfully'),
            AddressResource::collection($addresses)
        );
    }

    public function store(StoreAddressRequest $request)
    {
        $address = $this->service->store(
            auth('sanctum')->user()->id,
            $request->validated()
        );

        return ApiResponse::sendResponse(
            201,
            __('front.user-address-add-successfully'),
            new AddressResource($address)
        );
    }

    public function show(int $id)
    {
        $address = $this->service->show(auth('sanctum')->user()->id, $id);

        return ApiResponse::sendResponse(
            200,
            __('front.address-retrieved-successfully'),
            new AddressResource($address)
        );
    }

    public function update(UpdateAddressRequest $request, int $id)
    {
        $address = $this->service->update(
            auth('sanctum')->user()->id,
            $id,
            $request->validated()
        );

        return ApiResponse::sendResponse(
            200,
            __('front.user-address-update-successfully'),
            new AddressResource($address)
        );
    }

    public function destroy(DeleteAddressRequest $request, int $id)
    {
        $address = $this->service->delete(auth('sanctum')->user()->id, $id);

        return ApiResponse::sendResponse(
            200,
            __('front.address-deleted-successfully'),
            []
        );
    }

    public function setDefault(SetDefaultAddressRequest $request, int $id)
    {
        $address = $this->service->setDefault(auth('sanctum')->user()->id, $id);

        return ApiResponse::sendResponse(
            200,
            __('front.address-set-default-successfully'),
            new AddressResource($address)
        );
    }
}
