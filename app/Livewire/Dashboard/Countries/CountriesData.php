<?php

namespace App\Livewire\Dashboard\Countries;

use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class CountriesData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshData' => '$refresh',
        'deleteItem',
    ];

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function editCountry(int $id): void
    {
        $this->dispatch('countryUpdate', id: $id)->to(CountryUpdate::class);
    }

    public function manageGovernorates(int $id): void
    {
        $this->dispatch('governoratesManage', id: $id)->to(Governorates::class);
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('countryDelete', id: $id);
    }

    public function updateStatus(int $itemId, int $newStatus): void
    {
        $item = Country::query()->find($itemId);

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
        $item = Country::query()->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $item->delete();
        $this->dispatch('itemDeleted');
    }

    public function render()
    {
        $search = trim($this->search);
        $locale = app()->getLocale();

        $data = Country::query()
            ->withCount('governorates')
            ->when($search !== '', function ($query) use ($search, $locale) {
                $query->where(function ($q) use ($search, $locale) {
                    // Spatie Translatable JSON search
                    $q->where("name->{$locale}", 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.countries.countries-data', compact('data'));
    }
}
