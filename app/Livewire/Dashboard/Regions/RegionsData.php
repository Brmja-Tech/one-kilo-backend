<?php

namespace App\Livewire\Dashboard\Regions;

use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class RegionsData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $governorateId;

    public string $search = '';

    public string $statusFilter = 'all';

    public int $perPage = 10;

    protected $listeners = [
        'refreshData' => '$refresh',
        'deleteItem',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function editRegion(int $id): void
    {
        $this->dispatch('regionUpdate', id: $id)->to(RegionUpdate::class);
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('regionDelete', id: $id);
    }

    public function updateStatus(int $itemId, int $newStatus): void
    {
        $item = Region::query()
            ->where('governorate_id', $this->governorateId)
            ->find($itemId);

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
        $item = Region::query()
            ->where('governorate_id', $this->governorateId)
            ->withCount('addresses')
            ->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        if ((int) $item->addresses_count > 0) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.region-cannot-delete-has-addresses'));

            return;
        }

        $item->delete();

        $this->dispatch('refreshData');
        $this->dispatch('itemDeleted');
    }

    public function render()
    {
        $search = trim($this->search);
        $locale = app()->getLocale();

        $items = Region::query()
            ->where('governorate_id', $this->governorateId)
            ->when($search !== '', function (Builder $query) use ($search, $locale) {
                $query->where(function (Builder $subQuery) use ($search, $locale) {
                    $subQuery->where("name->{$locale}", 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%");
                });
            })
            ->when($this->statusFilter === 'active', fn (Builder $query) => $query->where('status', true))
            ->when($this->statusFilter === 'inactive', fn (Builder $query) => $query->where('status', false))
            ->latest('id')
            ->paginate($this->perPage);

        return view('dashboard.regions.regions-data', compact('items'));
    }
}

