<?php

namespace App\Livewire\Dashboard\Governorates;

use App\Models\Governorate;
use Livewire\Component;

class GovernorateUpdate extends Component
{
    public int $countryId;

    public ?int $governorateId = null;

    public string $name_ar = '';

    public string $name_en = '';

    public bool $status = true;

    protected $listeners = ['governorateUpdate'];

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2'],
            'name_en' => ['required', 'string', 'min:2'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function governorateUpdate(int $id): void
    {
        $item = Governorate::query()
            ->where('country_id', $this->countryId)
            ->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->governorateId = $item->id;
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

        $item = Governorate::query()
            ->where('country_id', $this->countryId)
            ->find($this->governorateId);

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
        $this->dispatch('refreshData')->to(GovernoratesData::class);
    }

    public function render()
    {
        return view('dashboard.governorates.governorate-update');
    }
}

