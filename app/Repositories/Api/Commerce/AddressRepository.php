<?php

namespace App\Repositories\Api\Commerce;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository
{
    public function listActiveForUser(int $userId): Collection
    {
        return Address::query()
            ->where('user_id', $userId)
            ->active()
            ->with(['country', 'governorate'])
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();
    }

    public function findActiveForUser(int $userId, int $addressId): Address
    {
        return Address::query()
            ->where('user_id', $userId)
            ->active()
            ->whereKey($addressId)
            ->with(['country', 'governorate'])
            ->firstOrFail();
    }

    public function createForUser(int $userId, array $data): Address
    {
        $address = Address::query()->create([
            ...$data,
            'user_id' => $userId,
        ]);

        return $this->loadDetails($address);
    }

    public function update(Address $address, array $data): Address
    {
        $address->update($data);

        return $this->loadDetails($address);
    }

    public function deactivate(Address $address): Address
    {
        $address->update([
            'status' => false,
            'is_default' => false,
        ]);

        return $address;
    }

    public function loadDetails(Address $address): Address
    {
        return $address->load(['country', 'governorate']);
    }

    public function unsetDefaultForUser(int $userId, ?int $exceptAddressId = null): void
    {
        Address::query()
            ->where('user_id', $userId)
            ->where('status', true)
            ->when($exceptAddressId, fn ($query) => $query->where('id', '!=', $exceptAddressId))
            ->update(['is_default' => false]);
    }

    public function hasActiveAddresses(int $userId): bool
    {
        return Address::query()
            ->where('user_id', $userId)
            ->active()
            ->exists();
    }

    public function hasActiveDefaultAddress(int $userId): bool
    {
        return Address::query()
            ->where('user_id', $userId)
            ->active()
            ->where('is_default', true)
            ->exists();
    }

    public function assignLatestActiveAsDefault(int $userId): ?Address
    {
        $address = Address::query()
            ->where('user_id', $userId)
            ->active()
            ->latest('id')
            ->first();

        if (! $address) {
            return null;
        }

        $address->update(['is_default' => true]);

        return $this->loadDetails($address);
    }
}
