<?php

namespace App\Livewire\Dashboard\Countries;

use App\Models\Country;
use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Livewire\Component;

class CountryUpdate extends Component
{
    protected $listeners = ['countryUpdate'];

    public ?int $countryId = null;

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
                UniqueTranslationRule::for('countries', 'name')->ignore($this->countryId),
            ],
            'name_en' => [
                'required',
                'string',
                'min:2',
                UniqueTranslationRule::for('countries', 'name')->ignore($this->countryId),
            ],
            'status' => ['required', 'boolean'],
        ];
    }

    public function countryUpdate(int $id): void
    {
        $item = Country::query()->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->countryId = $item->id;
        $this->name_ar = $item->getTranslation('name', 'ar') ?? '';
        $this->name_en = $item->getTranslation('name', 'en') ?? '';
        $this->status = (bool) $item->status;
        $this->resetValidation();

        $this->dispatch('updateModalToggle');
    }

    public function submit(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->validate();

        $item = Country::query()->find($this->countryId);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $item->update([
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'status' => $this->status,
        ]);

        $this->dispatch('notify', type: 'success', message: __('dashboard.update-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(CountriesData::class);
    }

    public function render()
    {
        return view('dashboard.countries.country-update');
    }
}
