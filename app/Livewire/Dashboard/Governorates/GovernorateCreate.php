<?php

namespace App\Livewire\Dashboard\Governorates;

use App\Models\Governorate;
use Livewire\Component;

class GovernorateCreate extends Component
{
    public int $countryId;

    public string $name_ar = '';

    public string $name_en = '';

    public bool $status = true;

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2'],
            'name_en' => ['required', 'string', 'min:2'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function submit(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->validate();

        Governorate::query()->create([
            'country_id' => $this->countryId,
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'status' => $this->status,
        ]);

        $this->reset(['name_ar', 'name_en', 'status']);
        $this->status = true;
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.add-successfully'));
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(GovernoratesData::class);
    }

    public function render()
    {
        return view('dashboard.governorates.governorate-create');
    }
}

