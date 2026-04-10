<?php

namespace App\Livewire\Dashboard\Governorates;

use App\Models\Governorate;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class GovernoratesData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $countryId;

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

    public function editGovernorate(int $id): void
    {
        $this->dispatch('governorateUpdate', id: $id)->to(GovernorateUpdate::class);
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('governorateDelete', id: $id);
    }

    public function updateStatus(int $itemId, int $newStatus): void
    {
        $item = Governorate::query()
            ->where('country_id', $this->countryId)
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
        $item = Governorate::query()
            ->where('country_id', $this->countryId)
            ->withCount('regions')
            ->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        if ((int) $item->regions_count > 0) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.governorate-cannot-delete-has-regions'));

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

        $items = Governorate::query()
            ->where('country_id', $this->countryId)
            ->withCount('regions')
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

        return view('dashboard.governorates.governorates-data', compact('items'));
    }
}

