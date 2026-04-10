<?php

namespace App\Livewire\Dashboard\Variants;

use App\Models\Variant;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VariantCreate extends Component
{
    public string $name_ar = '';
    public string $name_en = '';
    public string $key = '';
    public bool $status = true;
    public int $sort_order = 0;
    public array $items = [];

    protected $listeners = [
        'refreshData' => '$refresh',
    ];

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2', UniqueTranslationRule::for('variants', 'name')],
            'name_en' => ['required', 'string', 'min:2', UniqueTranslationRule::for('variants', 'name')],
            'key' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('variants', 'key')],
            'status' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name_ar' => ['required', 'string', 'min:1'],
            'items.*.name_en' => ['required', 'string', 'min:1'],
            'items.*.status' => ['required', 'boolean'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
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
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function submit(): void
    {
        $this->normalizeFormState();
        $this->validate();
        $this->validateUniqueItemNamesInForm();

        $variant = Variant::create([
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'key' => $this->key,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ]);

        foreach ($this->items as $item) {
            $variant->items()->create([
                'name' => [
                    'ar' => $item['name_ar'],
                    'en' => $item['name_en'],
                ],
                'name_plain' => trim((string) ($item['name_en'] ?? '')),
                'status' => $item['status'],
                'sort_order' => $item['sort_order'],
            ]);
        }

        $this->resetForm();
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.variant-add-successfully'));
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        return view('dashboard.variants.variant-create');
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

    protected function resetForm(): void
    {
        $this->name_ar = '';
        $this->name_en = '';
        $this->key = '';
        $this->status = true;
        $this->sort_order = 0;
        $this->items = [];
    }
}
