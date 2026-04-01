<?php

namespace App\Livewire\Dashboard\Categories;

use App\Models\Category;
use App\Utils\ImageManger;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryUpdate extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'categoryUpdate',
        'refreshData' => '$refresh',
    ];

    protected ImageManger $imageManger;

    public ?int $categoryId = null;

    public string $name_ar = '';

    public string $name_en = '';

    public $parent_id = '';

    public string $color = '#1946B9';

    public $image = null;

    public ?string $currentImage = null;

    public bool $status = true;

    public $sort_order = '';

    public function boot(ImageManger $imageManger): void
    {
        $this->imageManger = $imageManger;
    }

    public function rules(): array
    {
        return [
            'name_ar' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('categories', 'name')->ignore($this->categoryId),
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('categories', 'name')->ignore($this->categoryId),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                function ($attribute, $value, $fail) {
                    if ($value === null) {
                        return;
                    }

                    if (in_array((int) $value, $this->blockedParentIds(), true)) {
                        $fail(__('dashboard.category-invalid-parent'));
                    }
                },
            ],
            'color' => [
                'required',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
                function ($attribute, $value, $fail) {
                    $databaseColor = $this->pickerColorToDatabase($value);
                    $validator = validator(['color' => $databaseColor], ['color' => Category::colorValidationRules()]);

                    if ($validator->fails()) {
                        $fail($validator->errors()->first('color'));
                    }
                },
            ],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp,svg', 'max:2048'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function categoryUpdate(int $id): void
    {
        $item = Category::query()->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->categoryId = $item->id;
        $this->name_ar = $item->getTranslation('name', 'ar') ?? '';
        $this->name_en = $item->getTranslation('name', 'en') ?? '';
        $this->parent_id = $item->parent_id;
        $this->color = $this->databaseColorToPicker($item->color);
        $this->image = null;
        $this->currentImage = $item->image;
        $this->status = (bool) $item->status;
        $this->sort_order = $item->sort_order;
        $this->resetValidation();

        $this->dispatch('updateModalToggle');
    }

    public function submit(): void
    {
        $item = Category::query()->find($this->categoryId);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->color = $this->normalizePickerColor($this->color);
        $this->parent_id = $this->normalizeNullableInteger($this->parent_id);
        $this->sort_order = $this->normalizeNullableInteger($this->sort_order);

        $this->validate();

        $data = [
            'parent_id' => $this->parent_id,
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'color' => $this->pickerColorToDatabase($this->color),
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ];

        if ($this->image) {
            if ($this->shouldDeleteManagedImage($item->image)) {
                $this->imageManger->deleteImage($item->image);
            }

            $data['image'] = $this->imageManger->uploadImage('uploads/categories', $this->image, 'public');
        }

        $item->update($data);

        $this->currentImage = $item->fresh()->image;
        $this->dispatch('notify', type: 'success', message: __('dashboard.category-updated-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData');
    }

    protected function normalizeNullableInteger($value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
    }

    protected function normalizePickerColor(?string $value): string
    {
        return strtoupper(trim((string) $value));
    }

    protected function pickerColorToDatabase(?string $value): string
    {
        return '0xFF' . ltrim($this->normalizePickerColor($value), '#');
    }

    protected function databaseColorToPicker(?string $value): string
    {
        $normalized = strtoupper(trim((string) $value));

        if (preg_match(Category::COLOR_REGEX, $normalized)) {
            return '#' . substr($normalized, 4, 6);
        }

        return '#1946B9';
    }

    protected function shouldDeleteManagedImage(?string $path): bool
    {
        return filled($path) && str_starts_with($path, 'uploads/categories/');
    }

    protected function blockedParentIds(): array
    {
        if (! $this->categoryId) {
            return [];
        }

        $categories = Category::query()
            ->select(['id', 'parent_id'])
            ->get();

        $childrenByParent = $categories->groupBy(
            fn (Category $category) => $category->parent_id ?? 0
        );

        $ids = [$this->categoryId];
        $queue = [$this->categoryId];

        while ($queue !== []) {
            $parentId = array_shift($queue);

            foreach ($childrenByParent->get($parentId, collect()) as $child) {
                $ids[] = $child->id;
                $queue[] = $child->id;
            }
        }

        return array_values(array_unique($ids));
    }

    protected function parentOptions(array $excludedIds = []): array
    {
        $categories = Category::query()
            ->select(['id', 'parent_id', 'name', 'sort_order'])
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = $categories->groupBy(fn (Category $category) => $category->parent_id ?? 0);

        return $this->flattenParentOptions($grouped, 0, '', $excludedIds);
    }

    protected function flattenParentOptions(
        Collection $grouped,
        int $parentId = 0,
        string $prefix = '',
        array $excludedIds = []
    ): array {
        $options = [];

        foreach ($grouped->get($parentId, collect()) as $category) {
            if (in_array($category->id, $excludedIds, true)) {
                continue;
            }

            $options[] = [
                'id' => $category->id,
                'label' => $prefix . $category->name,
            ];

            $options = [
                ...$options,
                ...$this->flattenParentOptions($grouped, $category->id, $prefix . '-- ', $excludedIds),
            ];
        }

        return $options;
    }

    public function render()
    {
        return view('dashboard.categories.category-update', [
            'parentOptions' => $this->parentOptions($this->blockedParentIds()),
        ]);
    }
}