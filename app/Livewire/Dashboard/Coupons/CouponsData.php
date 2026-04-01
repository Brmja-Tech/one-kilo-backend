<?php

namespace App\Livewire\Dashboard\Coupons;

use App\Models\Coupon;
use Livewire\Component;
use Livewire\WithPagination;

class CouponsData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshData' => '$refresh',
        'deleteItem',
    ];

    public string $search = '';

    public string $statusFilter = 'all';

    public string $typeFilter = 'all';

    public int $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function editCoupon(int $id): void
    {
        $this->dispatch('couponUpdate', id: $id)->to(CouponUpdate::class);
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('couponDelete', id: $id);
    }

    public function updateStatus(int $itemId, int $newStatus): void
    {
        $item = Coupon::query()->find($itemId);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $item->status = (bool) $newStatus;
        $item->save();

        $this->dispatch('notify', type: 'success', message: __('dashboard.update-successfully'));
    }

    public function deleteItem(int $id): void
    {
        $item = Coupon::query()->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        if ($item->usages()->exists() || (int) $item->used_count > 0) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.coupon-cannot-delete-used'));

            return;
        }

        $item->delete();

        $this->dispatch('itemDeleted');
    }

    public function render()
    {
        $search = trim($this->search);

        $items = Coupon::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%');
                });
            })
            ->when($this->statusFilter === 'active', fn ($query) => $query->where('status', true))
            ->when($this->statusFilter === 'inactive', fn ($query) => $query->where('status', false))
            ->when($this->typeFilter !== 'all', fn ($query) => $query->where('type', $this->typeFilter))
            ->latest('id')
            ->paginate($this->perPage);

        return view('dashboard.coupons.coupons-data', compact('items'));
    }
}
