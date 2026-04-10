<?php

namespace App\Livewire\Dashboard\Products;

use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductSkuItem;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ProductSkus extends Component
{
    public int $productId;

    public ?Product $product = null;

    public ?int $skuId = null;

    public ?string $sku = null;

    public string $price = '';

    public string $quantity = '0';

    public bool $status = true;

    public int $sort_order = 0;

    public array $skuAttributes = [];

    protected $listeners = [
        'deleteSku',
        'refreshData' => '$refresh',
    ];

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $this->product = Product::query()->findOrFail($productId);

        $this->resetForm();
    }

    public function rules(): array
    {
        return [
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('product_skus', 'sku')->ignore($this->skuId)],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'skuAttributes' => ['required', 'array', 'min:1'],
            'skuAttributes.*.variant_id' => ['required', 'integer', Rule::exists('variants', 'id')],
            'skuAttributes.*.variant_item_id' => ['required', 'integer', Rule::exists('variant_items', 'id')],
        ];
    }

    public function resetForm(): void
    {
        $this->skuId = null;
        $this->sku = null;
        $this->price = '';
        $this->quantity = '0';
        $this->status = true;
        $this->sort_order = 0;
        $this->skuAttributes = [
            [
                'variant_id' => null,
                'variant_item_id' => null,
            ],
        ];

        $this->resetValidation();
    }

    public function addAttributeRow(): void
    {
        $this->skuAttributes[] = [
            'variant_id' => null,
            'variant_item_id' => null,
        ];
    }

    public function removeAttributeRow(int $index): void
    {
        if (count($this->skuAttributes) <= 1) {
            return;
        }

        unset($this->skuAttributes[$index]);
        $this->skuAttributes = array_values($this->skuAttributes);
    }

    public function editSku(int $id): void
    {
        $sku = ProductSku::query()
            ->where('product_id', $this->productId)
            ->with(['items'])
            ->find($id);

        if (! $sku) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->skuId = $sku->id;
        $this->sku = $sku->sku;
        $this->price = (string) $sku->priceBeforeDiscount();
        $this->quantity = (string) ((int) $sku->quantity);
        $this->status = (bool) $sku->status;
        $this->sort_order = (int) ($sku->sort_order ?? 0);

        $this->skuAttributes = $sku->items
            ->map(fn(ProductSkuItem $item) => [
                'variant_id' => (int) $item->variant_id,
                'variant_item_id' => (int) $item->variant_item_id,
            ])
            ->values()
            ->all();

        if ($this->skuAttributes === []) {
            $this->skuAttributes = [
                ['variant_id' => null, 'variant_item_id' => null],
            ];
        }

        $this->resetValidation();
        $this->dispatch('updateModalToggle');
    }

    public function submit(): void
    {
        if (! $this->product) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        if (! $this->product->has_variants) {
            throw ValidationException::withMessages([
                'skuAttributes' => [__('dashboard.product-must-have-variants')],
            ]);
        }

        $this->normalizeFormState();
        $this->validate();
        $this->validateAttributes();

        $signature = ProductSku::signatureFromAttributes($this->skuAttributes);

        if ($signature === '') {
            throw ValidationException::withMessages([
                'skuAttributes' => [__('dashboard.sku-attributes-required')],
            ]);
        }

        $exists = ProductSku::query()
            ->where('product_id', $this->productId)
            ->where('signature', $signature)
            ->when($this->skuId, fn($query) => $query->where('id', '!=', $this->skuId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'skuAttributes' => [__('dashboard.sku-combination-must-be-unique')],
            ]);
        }

        DB::transaction(function () use ($signature) {
            $payload = [
                'product_id' => $this->productId,
                'signature' => $signature,
                'sku' => $this->sku,
                'image' => null,
                'price' => round((float) $this->price, 2),
                'quantity' => (int) $this->quantity,
                'status' => (bool) $this->status,
                'sort_order' => (int) $this->sort_order,
            ];

            if ($this->skuId) {
                $sku = ProductSku::query()
                    ->where('product_id', $this->productId)
                    ->findOrFail($this->skuId);

                $sku->update($payload);
            } else {
                $sku = ProductSku::query()->create($payload);
            }

            $sku->items()->delete();

            foreach ($this->skuAttributes as $attribute) {
                $sku->items()->create([
                    'variant_id' => (int) Arr::get($attribute, 'variant_id'),
                    'variant_item_id' => (int) Arr::get($attribute, 'variant_item_id'),
                ]);
            }
        });

        $this->dispatch('notify', type: 'success', message: $this->skuId ? __('dashboard.updated-successfully') : __('dashboard.created-successfully'));
        $this->dispatch($this->skuId ? 'updateModalToggle' : 'createModalToggle');
        $this->resetForm();
    }

    public function confirmDeleteSku(int $id): void
    {
        $this->dispatch('skuDelete', id: $id);
    }

    public function deleteSku(int $id): void
    {
        $sku = ProductSku::query()
            ->where('product_id', $this->productId)
            ->withCount(['orderItems', 'cartItems'])
            ->find($id);

        if (! $sku) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        if ((int) $sku->order_items_count > 0) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.sku-cannot-delete-has-orders'));

            return;
        }

        if ((int) $sku->cart_items_count > 0) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.sku-cannot-delete-in-carts'));

            return;
        }

        try {
            $sku->delete();
        } catch (\Throwable $exception) {
            report($exception);

            $this->dispatch('notify', type: 'error', message: __('dashboard.error_occurred'));

            return;
        }

        $this->dispatch('notify', type: 'success', message: __('dashboard.deleted-successfully'));
        $this->dispatch('refreshData');
    }

    protected function normalizeFormState(): void
    {
        $this->sku = $this->sku !== null ? trim((string) $this->sku) : null;
        $this->sku = $this->sku !== '' ? Str::upper($this->sku) : null;

        $this->price = trim($this->price);
        $this->quantity = trim($this->quantity);
    }

    protected function validateAttributes(): void
    {
        $variantIds = collect($this->skuAttributes)
            ->map(fn(array $attribute) => (int) Arr::get($attribute, 'variant_id'))
            ->filter()
            ->values();

        if ($variantIds->count() !== $variantIds->unique()->count()) {
            throw ValidationException::withMessages([
                'skuAttributes' => [__('dashboard.sku-duplicate-variant')],
            ]);
        }

        foreach ($this->skuAttributes as $index => $attribute) {
            $variantId = (int) Arr::get($attribute, 'variant_id');
            $itemId = (int) Arr::get($attribute, 'variant_item_id');

            $item = VariantItem::query()
                ->where('id', $itemId)
                ->where('variant_id', $variantId)
                ->where('status', true)
                ->first();

            $variantOk = Variant::query()
                ->where('id', $variantId)
                ->where('status', true)
                ->exists();

            if (! $variantOk || ! $item) {
                throw ValidationException::withMessages([
                    "skuAttributes.{$index}.variant_item_id" => [__('dashboard.sku-invalid-attribute')],
                ]);
            }
        }
    }

    public function render()
    {
        $product = $this->product ?? Product::query()->find($this->productId);

        $skus = ProductSku::query()
            ->where('product_id', $this->productId)
            ->with(['items.variant', 'items.item'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $variants = Variant::query()
            ->active()
            ->with('activeItems')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('dashboard.products.product-skus', [
            'product' => $product,
            'skus' => $skus,
            'variants' => $variants,
        ]);
    }
}
