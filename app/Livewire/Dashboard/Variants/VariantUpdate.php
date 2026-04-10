<?php

namespace App\Livewire\Dashboard\Variants;

use App\Models\ProductSkuItem;
use App\Models\Variant;
use App\Models\VariantItem;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VariantUpdate extends Component
{
    public ?int $variantId = null;
    public string $name_ar = '';
    public string $name_en = '';
    public string $key = '';
    public bool $status = true;
    public int $sort_order = 0;
    public array $items = [];

    protected $listeners = [
        'variantUpdate',
        'refreshData' => '$refresh',
    ];

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2', UniqueTranslationRule::for('variants', 'name')->ignore($this->variantId)],
            'name_en' => ['required', 'string', 'min:2', UniqueTranslationRule::for('variants', 'name')->ignore($this->variantId)],
            'key' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('variants', 'key')->ignore($this->variantId)],
            'status' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name_ar' => ['required', 'string', 'min:1'],
            'items.*.name_en' => ['required', 'string', 'min:1'],
            'items.*.status' => ['required', 'boolean'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    public function variantUpdate(int $id): void
    {
        $variant = Variant::query()
            ->with('items')
            ->find($id);

        if (! $variant) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->variantId = $variant->id;
        $this->name_ar = $variant->getTranslation('name', 'ar') ?? '';
        $this->name_en = $variant->getTranslation('name', 'en') ?? '';
        $this->key = $variant->key ?? '';
        $this->status = (bool) $variant->status;
        $this->sort_order = (int) ($variant->sort_order ?? 0);

        $this->items = $variant->items->map(fn (VariantItem $item) => [
            'id' => $item->id,
            'name_ar' => $item->getTranslation('name', 'ar') ?? '',
            'name_en' => $item->getTranslation('name', 'en') ?? '',
            'status' => (bool) $item->status,
            'sort_order' => (int) ($item->sort_order ?? 0),
        ])->all();

        $this->resetValidation();

        $this->dispatch('updateModalToggle');
    }

    public function addItem(): void
    {
        $this->items[] = [
            'name_ar' => '',
            'name_en' => '',
            'status' => true,
            'sort_order' => count($this->items),
        ];
    }

    public function removeItem(int $index): void
    {
        if (! array_key_exists($index, $this->items)) {
            return;
        }

        $itemId = $this->items[$index]['id'] ?? null;

        if ($itemId) {
            $item = VariantItem::query()->find($itemId);

            if (! $item) {
                return;
            }

            $inUse = ProductSkuItem::query()
                ->where('variant_item_id', $itemId)
                ->exists();

            if ($inUse) {
                $this->dispatch('notify', type: 'error', message: __('dashboard.variant-item-cannot-delete-in-use'));

                return;
            }

            $item->delete();
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function submit(): void
    {
        $variant = Variant::query()->with('items')->find($this->variantId);

        if (! $variant) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->normalizeFormState();
        $this->validate();
        $this->validateUniqueItemNamesInForm();
        $this->validateItemNamesAreUniqueInDatabase($variant->id);

        $variant->update([
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'key' => $this->key,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ]);

        foreach ($this->items as $item) {
            if (isset($item['id'])) {
                $variant->items()->find($item['id'])?->update([
                    'name' => [
                        'ar' => $item['name_ar'],
                        'en' => $item['name_en'],
                    ],
                    'name_plain' => trim((string) ($item['name_en'] ?? '')),
                    'meta' => null,
                    'status' => $item['status'],
                    'sort_order' => $item['sort_order'],
                ]);
            } else {
                $variant->items()->create([
                    'name' => [
                        'ar' => $item['name_ar'],
                        'en' => $item['name_en'],
                    ],
                    'name_plain' => trim((string) ($item['name_en'] ?? '')),
                    'meta' => null,
                    'status' => $item['status'],
                    'sort_order' => $item['sort_order'],
                ]);
            }
        }

        $this->dispatch('notify', type: 'success', message: __('dashboard.variant-update-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        return view('dashboard.variants.variant-update');
    }

    protected function normalizeFormState(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);

        $this->key = trim($this->key);
        $this->key = $this->key !== '' ? Str::slug($this->key) : Str::slug($this->name_en);

        foreach ($this->items as $index => $item) {
            $this->items[$index]['name_ar'] = trim((string) ($item['name_ar'] ?? ''));
            $this->items[$index]['name_en'] = trim((string) ($item['name_en'] ?? ''));
        }
    }

    protected function validateUniqueItemNamesInForm(): void
    {
        $names = array_map(
            fn (array $item) => trim((string) ($item['name_en'] ?? '')),
            $this->items
        );

        $duplicates = collect($names)
            ->filter(fn (string $name) => $name !== '')
            ->countBy()
            ->filter(fn (int $count) => $count > 1)
            ->keys()
            ->all();

        if ($duplicates !== []) {
            throw ValidationException::withMessages([
                'items' => [__('dashboard.variant-items-duplicate-names')],
            ]);
        }
    }

    protected function validateItemNamesAreUniqueInDatabase(int $variantId): void
    {
        foreach ($this->items as $index => $item) {
            $namePlain = trim((string) ($item['name_en'] ?? ''));
            $itemId = $item['id'] ?? null;

            if ($namePlain === '') {
                continue;
            }

            $exists = VariantItem::query()
                ->where('variant_id', $variantId)
                ->where('name_plain', $namePlain)
                ->when($itemId, fn ($query) => $query->where('id', '!=', $itemId))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    "items.{$index}.name_en" => [__('validation.unique', ['attribute' => __('dashboard.name-en')])],
                ]);
            }
        }
    }
}
