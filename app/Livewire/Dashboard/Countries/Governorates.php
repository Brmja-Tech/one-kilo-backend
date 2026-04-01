<?php

namespace App\Livewire\Dashboard\Countries;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Governorates extends Component
{
    public ?Country $country = null;

    /**
     * rows structure:
     * [
     *   [
     *     'id' => int|null,
     *     'name_ar' => string,
     *     'name_en' => string,
     *     'shipping_price' => string|float,
     *     'status' => bool,
     *   ]
     * ]
     */
    public array $rows = [];

    protected $listeners = ['governoratesManage'];

    public function governoratesManage(int $id): void
    {
        $country = Country::with('governorates')->find($id);

        if (! $country) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->country = $country;

        $this->rows = $this->country->governorates
            ->sortBy('id')
            ->map(function ($g) {
                return [
                    'id'                => $g->id,
                    'name_ar'           => $g->getTranslation('name', 'ar') ?? '',
                    'name_en'           => $g->getTranslation('name', 'en') ?? '',
                    'shipping_price'    => (string) ($g->shipping_price ?? 0),
                    'status'            => (bool) $g->status,
                ];
            })
            ->values()
            ->toArray();

        $this->resetValidation();
        $this->dispatch('showModalToggle');
    }

    public function rules(): array
    {
        return [
            'rows' => ['array'],
            'rows.*.name_ar' => ['required', 'string', 'min:2'],
            'rows.*.name_en' => ['required', 'string', 'min:2'],
            'rows.*.shipping_price' => ['required', 'numeric', 'min:0'],
            'rows.*.status' => ['required', 'boolean'],
        ];
    }

    public function addRow(): void
    {
        $this->rows[] = [
            'id'             => null,
            'name_ar'        => '',
            'name_en'        => '',
            'shipping_price' => '0',
            'status'         => true,
        ];
    }

    public function removeRow(int $index): void
    {
        if (! isset($this->rows[$index])) {
            return;
        }

        $row = $this->rows[$index];

        if (! empty($row['id']) && $this->country) {
            Governorate::query()
                ->where('country_id', $this->country->id)
                ->where('id', $row['id'])
                ->delete();
        }

        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);

        $this->dispatch('notify', type: 'success', message: __('dashboard.delete-successfully'));
        $this->dispatch('refreshData')->to(CountriesData::class);
    }

    public function save(): void
    {
        if (! $this->country) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->validate();

        DB::transaction(function () {
            foreach ($this->rows as $i => $row) {
                $gov = null;

                if (! empty($row['id'])) {
                    $gov = Governorate::query()
                        ->where('country_id', $this->country->id)
                        ->where('id', $row['id'])
                        ->first();
                }

                if (! $gov) {
                    $gov = new Governorate();
                    $gov->country_id = $this->country->id;
                }

                $gov->name = [
                    'ar' => trim($row['name_ar']),
                    'en' => trim($row['name_en']),
                ];

                $gov->shipping_price = (float) $row['shipping_price'];
                $gov->status = (bool) $row['status'];
                $gov->save();

                $this->rows[$i]['id'] = $gov->id;
            }
        });

        $this->dispatch('notify', type: 'success', message: __('dashboard.update-successfully'));
        $this->dispatch('refreshData')->to(CountriesData::class);
        $this->dispatch('showModalToggle');
        $this->resetValidation();
    }

    public function render()
    {
        return view('dashboard.countries.governorates');
    }
}
