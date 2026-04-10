<?php

namespace App\Livewire\Dashboard\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Utils\ImageManger;
use Carbon\Carbon;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductUpdate extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'productUpdate' => 'loadItem',
        'refreshData' => '$refresh',
    ];

    protected ImageManger $imageManger;

    public ?int $productId = null;

    public ?string $productSlug = null;

    public $category_id = '';

    public string $name_ar = '';

    public string $name_en = '';

    public $short_description_ar = '';

    public $short_description_en = '';

    public $description_ar = '';

    public $description_en = '';

    public $image = null;

    public ?string $currentImage = null;

    public array $gallery_images = [];

    public array $currentGallery = [];

    public array $removedGalleryImageIds = [];

    public string $price = '';

    public $discount_type = '';

    public $discount_value = '';

    public $discount_starts_at = '';

    public $discount_ends_at = '';

    public $sku = '';

    public string $stock = '0';

    public bool $is_featured = false;

    public bool $status = true;

    public bool $has_variants = false;

    public function boot(ImageManger $imageManger): void
    {
        $this->imageManger = $imageManger;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name_ar' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('products', 'name')->ignore($this->productId),
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('products', 'name')->ignore($this->productId),
            ],
            'short_description_ar' => ['nullable', 'string', 'max:255'],
            'short_description_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg', 'max:2048'],
            'currentGallery.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'has_variants' => ['required', 'boolean'],
            'price' => [$this->has_variants ? 'nullable' : 'required', 'numeric', 'min:0'],
            'discount_type' => [
                'nullable',
                'required_with:discount_value,discount_starts_at,discount_ends_at',
                Rule::in(['amount', 'percentage']),
            ],
            'discount_value' => ['nullable', 'required_with:discount_type', 'numeric', 'gt:0'],
            'discount_starts_at' => ['nullable', 'date'],
            'discount_ends_at' => ['nullable', 'date', 'after_or_equal:discount_starts_at'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($this->productId)],
            'stock' => [$this->has_variants ? 'nullable' : 'required', 'integer', 'min:0'],
            'is_featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function loadItem(int $id): void
    {
        $item = Product::query()
            ->with('images')
            ->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->productId = $item->id;
        $this->productSlug = $item->slug;
        $this->category_id = $item->category_id;
        $this->name_ar = $item->getTranslation('name', 'ar') ?? '';
        $this->name_en = $item->getTranslation('name', 'en') ?? '';
        $this->short_description_ar = $item->getTranslation('short_description', 'ar') ?? '';
        $this->short_description_en = $item->getTranslation('short_description', 'en') ?? '';
        $this->description_ar = $item->getTranslation('description', 'ar') ?? '';
        $this->description_en = $item->getTranslation('description', 'en') ?? '';
        $this->image = null;
        $this->currentImage = $item->image;
        $this->gallery_images = [];
        $this->currentGallery = $this->mapGallery($item->images);
        $this->removedGalleryImageIds = [];

        $this->price = $item->price ? (string) $item->price : '';
        $this->discount_type = $item->discount_type ?? '';
        $this->discount_value = $item->discount_value !== null ? (string) $item->discount_value : '';
        $this->discount_starts_at = $item->discount_starts_at?->format('Y-m-d\\TH:i') ?? '';
        $this->discount_ends_at = $item->discount_ends_at?->format('Y-m-d\\TH:i') ?? '';
        $this->sku = $item->sku ?? '';
        $this->stock = (string) ($item->stock ?? 0);
        $this->is_featured = (bool) $item->is_featured;
        $this->status = (bool) $item->status;
        $this->has_variants = (bool) $item->has_variants;

        if ($this->has_variants) {
            $this->price = '';
            $this->sku = '';
            $this->stock = '0';
        }

        $this->resetValidation();
        $this->dispatch('updateModalToggle');
    }

    public function removeExistingGalleryImage(int $imageId): void
    {
        $exists = collect($this->currentGallery)->contains(
            fn(array $image) => (int) $image['id'] === $imageId
        );

        if (! $exists) {
            return;
        }

        if (! in_array($imageId, $this->removedGalleryImageIds, true)) {
            $this->removedGalleryImageIds[] = $imageId;
        }

        $this->currentGallery = array_values(array_filter(
            $this->currentGallery,
            fn(array $image) => (int) $image['id'] !== $imageId
        ));
    }

    public function removeNewGalleryImage(int $index): void
    {
        if (! array_key_exists($index, $this->gallery_images)) {
            return;
        }

        unset($this->gallery_images[$index]);
        $this->gallery_images = array_values($this->gallery_images);
    }

    public function submit(): void
    {
        $product = Product::query()
            ->with('images')
            ->find($this->productId);

        if (! $product) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->normalizeFormState();
        $this->validate();

        if (! $this->passesDiscountRules()) {
            return;
        }

        $newMainImagePath = null;
        $newGalleryPaths = [];
        $oldMainImage = $product->image;
        $removedImages = $product->images->whereIn('id', $this->removedGalleryImageIds)->values();

        try {
            if ($this->image) {
                $newMainImagePath = $this->imageManger->uploadImage('uploads/products', $this->image, 'public');
            }

            if ($this->gallery_images !== []) {
                $newGalleryPaths = $this->imageManger->uploadMultiImage(
                    'uploads/products/gallery',
                    $this->gallery_images,
                    'public'
                );
            }

            DB::transaction(function () use ($product, $newMainImagePath, $newGalleryPaths): void {
                $product->update($this->payload($newMainImagePath));

                foreach ($this->currentGallery as $galleryImage) {
                    ProductImage::query()
                        ->whereKey($galleryImage['id'])
                        ->where('product_id', $product->id)
                        ->update([
                            'sort_order' => $galleryImage['sort_order'],
                        ]);
                }

                if ($this->removedGalleryImageIds !== []) {
                    ProductImage::query()
                        ->where('product_id', $product->id)
                        ->whereIn('id', $this->removedGalleryImageIds)
                        ->delete();
                }

                if ($newGalleryPaths !== []) {
                    $startOrder = collect($this->currentGallery)
                        ->pluck('sort_order')
                        ->filter(fn($value) => $value !== null)
                        ->map(fn($value) => (int) $value)
                        ->max() ?? 0;

                    ProductImage::query()->insert(
                        collect($newGalleryPaths)->values()->map(fn(string $path, int $index) => [
                            'product_id' => $product->id,
                            'image' => $path,
                            'sort_order' => $startOrder + $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ])->all()
                    );
                }
            });
        } catch (\Throwable $exception) {
            foreach (array_filter([$newMainImagePath, ...$newGalleryPaths]) as $path) {
                if ($this->shouldDeleteManagedImage($path)) {
                    $this->imageManger->deleteImage($path);
                }
            }

            report($exception);

            $this->dispatch('notify', type: 'error', message: __('dashboard.error_occurred'));

            return;
        }

        if ($newMainImagePath && $this->shouldDeleteManagedImage($oldMainImage)) {
            $this->imageManger->deleteImage($oldMainImage);
        }

        foreach ($removedImages as $removedImage) {
            if ($this->shouldDeleteManagedImage($removedImage->image)) {
                $this->imageManger->deleteImage($removedImage->image);
            }
        }

        $this->hydrateFromProduct($product->fresh(['images']));

        $this->dispatch('notify', type: 'success', message: __('dashboard.product-updated-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(ProductsData::class);
    }

    protected function hydrateFromProduct(Product $product): void
    {
        $this->productId = $product->id;
        $this->productSlug = $product->slug;
        $this->currentImage = $product->image;
        $this->currentGallery = $this->mapGallery($product->images);
        $this->removedGalleryImageIds = [];
    }

    protected function payload(?string $newMainImagePath = null): array
    {
        $data = [
            'category_id' => (int) $this->category_id,
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'short_description' => [
                'ar' => $this->short_description_ar,
                'en' => $this->short_description_en,
            ],
            'description' => [
                'ar' => $this->description_ar,
                'en' => $this->description_en,
            ],
            'price' => $this->has_variants ? null : round((float) $this->price, 2),
            'discount_type' => $this->discount_type ?: null,
            'discount_value' => $this->discount_type ? round((float) $this->discount_value, 2) : null,
            'discount_starts_at' => $this->discount_type ? $this->nullableDateTime($this->discount_starts_at) : null,
            'discount_ends_at' => $this->discount_type ? $this->nullableDateTime($this->discount_ends_at) : null,
            'sku' => $this->has_variants ? null : ($this->sku ?: null),
            'stock' => $this->has_variants ? 0 : (int) $this->stock,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'has_variants' => $this->has_variants,
        ];

        if ($newMainImagePath) {
            $data['image'] = $newMainImagePath;
        }

        return $data;
    }

    protected function passesDiscountRules(): bool
    {
        if ($this->discount_type === 'percentage' && (float) $this->discount_value > 100) {
            $this->addError(
                'discount_value',
                __('validation.max.numeric', ['attribute' => __('dashboard.percentage'), 'max' => 100])
            );

            return false;
        }

        return true;
    }

    protected function normalizeFormState(): void
    {
        $this->category_id = $this->normalizeNullableInteger($this->category_id);

        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->short_description_ar = $this->normalizeNullableString($this->short_description_ar);
        $this->short_description_en = $this->normalizeNullableString($this->short_description_en);
        $this->description_ar = $this->normalizeNullableString($this->description_ar);
        $this->description_en = $this->normalizeNullableString($this->description_en);

        $this->discount_type = $this->normalizeNullableString($this->discount_type);
        $this->discount_value = $this->normalizeNullableString($this->discount_value);
        $this->discount_starts_at = $this->normalizeNullableString($this->discount_starts_at);
        $this->discount_ends_at = $this->normalizeNullableString($this->discount_ends_at);

        $this->sku = $this->normalizeNullableString($this->sku);
        $this->sku = $this->sku ? Str::upper($this->sku) : null;

        $this->price = trim((string) $this->price);
        $this->stock = trim((string) $this->stock);

        $this->currentGallery = array_values(array_map(function (array $image): array {
            $image['sort_order'] = $this->normalizeNullableInteger($image['sort_order'] ?? null);

            return $image;
        }, $this->currentGallery));

        $this->removedGalleryImageIds = array_values(array_unique(array_map(
            fn($id) => (int) $id,
            $this->removedGalleryImageIds
        )));

        if ($this->has_variants) {
            $this->price = '';
            $this->stock = '0';
            $this->sku = null;
        }
    }

    protected function mapGallery($images): array
    {
        return collect($images)->map(fn(ProductImage $image) => [
            'id' => $image->id,
            'image' => $image->image,
            'sort_order' => $image->sort_order,
        ])->values()->all();
    }

    protected function normalizeNullableInteger($value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
    }

    protected function normalizeNullableString($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function nullableDateTime($value): ?string
    {
        $value = $this->normalizeNullableString($value);

        return $value === null ? null : Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    protected function shouldDeleteManagedImage(?string $path): bool
    {
        return filled($path) && str_starts_with($path, 'uploads/products/');
    }

    protected function categoryOptions(): array
    {
        $categories = Category::query()
            ->select(['id', 'parent_id', 'name', 'status', 'sort_order'])
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = $categories->groupBy(fn(Category $category) => $category->parent_id ?? 0);

        return $this->flattenCategoryOptions($grouped, 0);
    }

    protected function flattenCategoryOptions(
        Collection $grouped,
        int $parentId = 0,
        string $prefix = ''
    ): array {
        $options = [];

        foreach ($grouped->get($parentId, collect()) as $category) {
            $options[] = [
                'id' => $category->id,
                'label' => $prefix . $category->name . ($category->status ? '' : ' (' . __('dashboard.inactive') . ')'),
            ];

            $options = [
                ...$options,
                ...$this->flattenCategoryOptions($grouped, $category->id, $prefix . '-- '),
            ];
        }

        return $options;
    }

    public function render()
    {
        return view('dashboard.products.product-update', [
            'categoryOptions' => $this->categoryOptions(),
        ]);
    }
}
