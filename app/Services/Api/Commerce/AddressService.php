<?php

namespace App\Services\Api\Commerce;

use App\Models\Address;
use App\Repositories\Api\Commerce\AddressRepository;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function __construct(protected AddressRepository $addressRepository)
    {
    }

    public function list(int $userId): Collection
    {
        return $this->addressRepository->listActiveForUser($userId);
    }

    public function show(int $userId, int $addressId): Address
    {
        return $this->addressRepository->findActiveForUser($userId, $addressId);
    }

    public function store(int $userId, array $data): Address
    {
        $shouldBeDefault = (bool) ($data['is_default'] ?? false);

        if (! $this->addressRepository->hasActiveAddresses($userId)) {
            $shouldBeDefault = true;
        }

        if ($shouldBeDefault) {
            $this->addressRepository->unsetDefaultForUser($userId);
        }

        return $this->addressRepository->createForUser($userId, [
            ...$data,
            'is_default' => $shouldBeDefault,
            'status' => true,
        ]);
    }

    public function update(int $userId, int $addressId, array $data): Address
    {
        $address = $this->addressRepository->findActiveForUser($userId, $addressId);

        if (($data['is_default'] ?? false) === true) {
            $this->addressRepository->unsetDefaultForUser($userId, $address->id);
        }

        $address = $this->addressRepository->update($address, $data);

        if (! $this->addressRepository->hasActiveDefaultAddress($userId)) {
            $this->addressRepository->unsetDefaultForUser($userId);
            $address = $this->addressRepository->update($address, ['is_default' => true]);
        }

        return $address;
    }

    public function setDefault(int $userId, int $addressId): Address
    {
        $address = $this->addressRepository->findActiveForUser($userId, $addressId);

        $this->addressRepository->unsetDefaultForUser($userId, $address->id);

        return $this->addressRepository->update($address, ['is_default' => true]);
    }

    public function delete(int $userId, int $addressId): Address
    {
        $address = $this->addressRepository->findActiveForUser($userId, $addressId);
        $wasDefault = (bool) $address->is_default;

        $address = $this->addressRepository->deactivate($address);

        if ($wasDefault) {
            $this->addressRepository->unsetDefaultForUser($userId);
            $this->addressRepository->assignLatestActiveAsDefault($userId);
        }

        return $address;
    }
}
