<?php

namespace App\Livewire\Dashboard\Regions;

use App\Models\Region;
use Livewire\Component;

class RegionUpdate extends Component
{
    public int $governorateId;

    public ?int $regionId = null;

    public string $name_ar = '';

    public string $name_en = '';

    public string $shipping_price = '0';

    public bool $status = true;

    protected $listeners = ['regionUpdate'];

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2'],
            'name_en' => ['required', 'string', 'min:2'],
            'shipping_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function regionUpdate(int $id): void
    {
        $item = Region::query()
            ->where('governorate_id', $this->governorateId)
            ->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->regionId = $item->id;
        $this->name_ar = $item->getTranslation('name', 'ar') ?? '';
        $this->name_en = $item->getTranslation('name', 'en') ?? '';
        $this->shipping_price = (string) ($item->shipping_price ?? 0);
        $this->status = (bool) $item->status;
        $this->resetValidation();

        $this->dispatch('updateModalToggle');
    }

    public function submit(): void
    {
        $this->name_ar = trim($this->name_ar);
        $this->name_en = trim($this->name_en);
        $this->validate();

        $item = Region::query()
            ->where('governorate_id', $this->governorateId)
            ->find($this->regionId);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $item->update([
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'shipping_price' => (float) $this->shipping_price,
            'status' => $this->status,
        ]);

        $this->dispatch('notify', type: 'success', message: __('dashboard.update-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(RegionsData::class);
    }

    public function render()
    {
        return view('dashboard.regions.region-update');
    }
}

