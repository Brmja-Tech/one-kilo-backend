<?php

namespace App\Livewire\Dashboard\Countries;

use App\Models\Country;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Livewire\Component;

class CountryCreate extends Component
{
    public string $name_ar = '';

    public string $name_en = '';

    public bool $status = true;

    public function rules(): array
    {
        return [
            'name_ar' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('countries', 'name'),
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('countries', 'name'),
            ],
            'status' => ['required', 'boolean'],
        ];
    }

    public function submit(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->validate();

        Country::query()->create([
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
        $this->dispatch('refreshData')->to(CountriesData::class);
    }

    public function render()
    {
        return view('dashboard.countries.country-create');
    }
}
