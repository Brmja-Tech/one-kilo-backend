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

class ProductCreate extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    protected ImageManger $imageManger;

    public $category_id = '';

    public string $name_ar = '';

    public string $name_en = '';

    public $short_description_ar = '';

    public $short_description_en = '';

    public $description_ar = '';

    public $description_en = '';

    public $image = null;

    public array $gallery_images = [];

    public string $price = '';

    public $discount_type = '';

    public $discount_value = '';

    public $discount_starts_at = '';

    public $discount_ends_at = '';

    public $sku = '';

    public string $stock = '0';

    public bool $is_featured = false;

    public bool $has_variants = false;

    public bool $status = true;

    public function boot(ImageManger $imageManger): void
    {
        $this->imageManger = $imageManger;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name_ar' => ['required', 'string', 'min:2', UniqueTranslationRule::for('products', 'name')],
            'name_en' => ['required', 'string', 'min:2', UniqueTranslationRule::for('products', 'name')],
            'short_description_ar' => ['nullable', 'string', 'max:255'],
            'short_description_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg', 'max:2048'],
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
            'sku' => [$this->has_variants ? 'nullable' : 'nullable', 'string', 'max:255', Rule::unique('products', 'sku')],
            'stock' => [$this->has_variants ? 'nullable' : 'required', 'integer', 'min:0'],
            'is_featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
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
        $this->normalizeFormState();
        $this->validate();

        if (! $this->passesDiscountRules()) {
            return;
        }

        $mainImagePath = null;
        $galleryPaths = [];

        try {
            $mainImagePath = $this->imageManger->uploadImage('uploads/products', $this->image, 'public');

            if ($this->gallery_images !== []) {
                $galleryPaths = $this->imageManger->uploadMultiImage(
                    'uploads/products/gallery',
                    $this->gallery_images,
                    'public'
                );
            }

            DB::transaction(function () use ($mainImagePath, $galleryPaths): void {
                $product = Product::query()->create($this->payload($mainImagePath));

                if ($galleryPaths !== []) {
                    ProductImage::query()->insert(
                        collect($galleryPaths)->values()->map(fn(string $path, int $index) => [
                            'product_id' => $product->id,
                            'image' => $path,
                            'sort_order' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ])->all()
                    );
                }
            });
        } catch (\Throwable $exception) {
            foreach (array_filter([$mainImagePath, ...$galleryPaths]) as $path) {
                if ($this->shouldDeleteManagedImage($path)) {
                    $this->imageManger->deleteImage($path);
                }
            }

            report($exception);

            $this->dispatch('notify', type: 'error', message: __('dashboard.error_occurred'));

            return;
        }

        $this->resetForm();
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.product-added-successfully'));
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(ProductsData::class);
    }

    protected function payload(string $mainImagePath): array
    {
        return [
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
            'image' => $mainImagePath,
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

        if ($this->has_variants) {
            $this->price = '';
            $this->stock = '0';
            $this->sku = null;
        }
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

    protected function resetForm(): void
    {
        $this->reset([
            'category_id',
            'name_ar',
            'name_en',
            'short_description_ar',
            'short_description_en',
            'description_ar',
            'description_en',
            'image',
            'gallery_images',
            'price',
            'discount_type',
            'discount_value',
            'discount_starts_at',
            'discount_ends_at',
            'sku',
            'stock',
            'is_featured',
            'status',
            'has_variants',
        ]);

        $this->stock = '0';
        $this->is_featured = false;
        $this->status = true;
        $this->has_variants = false;
    }

    public function render()
    {
        return view('dashboard.products.product-create', [
            'categoryOptions' => $this->categoryOptions(),
        ]);
    }
}
