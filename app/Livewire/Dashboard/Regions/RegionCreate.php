<?php

namespace App\Livewire\Dashboard\Regions;

use App\Models\Region;
use Livewire\Component;

class RegionCreate extends Component
{
    public int $governorateId;

    public string $name_ar = '';

    public string $name_en = '';

    public string $shipping_price = '0';

    public bool $status = true;

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2'],
            'name_en' => ['required', 'string', 'min:2'],
            'shipping_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function submit(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->validate();

        Region::query()->create([
            'governorate_id' => $this->governorateId,
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'shipping_price' => (float) $this->shipping_price,
            'status' => $this->status,
        ]);

        $this->reset(['name_ar', 'name_en', 'shipping_price', 'status']);
        $this->shipping_price = '0';
        $this->status = true;
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.add-successfully'));
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(RegionsData::class);
    }

    public function render()
    {
        return view('dashboard.regions.region-create');
    }
}

