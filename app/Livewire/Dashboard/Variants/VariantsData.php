<?php

namespace App\Livewire\Dashboard\Variants;

use App\Models\ProductSkuItem;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class VariantsData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshData' => '$refresh',
        'deleteItem',
    ];

    public string $search = '';

    public string $statusFilter = 'all';

    public int $perPage = 10;

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

    public function editVariant(int $id): void
    {
        $this->dispatch('variantUpdate', id: $id)->to(VariantUpdate::class);
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('variantDelete', id: $id);
    }

    public function updateStatus(int $itemId, int $newStatus): void
    {
        $item = Variant::query()->find($itemId);

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
        $variant = Variant::query()->find($id);

        if (! $variant) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $inUse = ProductSkuItem::query()
            ->where('variant_id', $variant->id)
            ->exists();

        if ($inUse) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.variant-cannot-delete-in-use'));

            return;
        }

        try {
            $variant->delete();
        } catch (\Throwable $exception) {
            report($exception);

            $this->dispatch('notify', type: 'error', message: __('dashboard.error_occurred'));

            return;
        }

        $this->dispatch('refreshData');
        $this->dispatch('itemDeleted');
    }

    public function render()
    {
        $search = trim($this->search);

        $items = Variant::query()
            ->withCount('items')
            ->when($search !== '', function (Builder $query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function (Builder $subQuery) use ($term) {
                    $subQuery->where('key', 'like', $term)
                        ->orWhere('name->ar', 'like', $term)
                        ->orWhere('name->en', 'like', $term);
                });
            })
            ->when($this->statusFilter === 'active', fn (Builder $query) => $query->where('status', true))
            ->when($this->statusFilter === 'inactive', fn (Builder $query) => $query->where('status', false))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($this->perPage);

        return view('dashboard.variants.variants-data', [
            'items' => $items,
        ]);
    }
}
