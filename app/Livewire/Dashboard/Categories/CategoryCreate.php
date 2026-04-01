<?php

namespace App\Livewire\Dashboard\Categories;

use App\Models\Category;
use App\Utils\ImageManger;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryCreate extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    protected ImageManger $imageManger;

    public string $name_ar = '';

    public string $name_en = '';

    public $parent_id = '';

    public string $color = '#1946B9';

    public $image = null;

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
                UniqueTranslationRule::for('categories', 'name'),
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('categories', 'name'),
            ],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
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

    public function submit(): void
    {
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
            $data['image'] = $this->imageManger->uploadImage('uploads/categories', $this->image, 'public');
        }

        Category::query()->create($data);

        $this->resetForm();
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.category-added-successfully'));
        $this->dispatch('createModalToggle');
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

    protected function resetForm(): void
    {
        $this->reset(['name_ar', 'name_en', 'parent_id', 'color', 'image', 'status', 'sort_order']);
        $this->color = '#1946B9';
        $this->status = true;
    }

    public function render()
    {
        return view('dashboard.categories.category-create', [
            'parentOptions' => $this->parentOptions(),
        ]);
    }
}