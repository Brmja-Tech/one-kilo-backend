<x-show-modal title="{{ __('dashboard.governorates') }}">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <div>
            <h5 class="mb-0">{{ $country?->name ?? '' }}</h5>
            <small class="text-muted">{{ __('dashboard.manage-governorates') }}</small>
        </div>
        <button type="button" class="btn btn-sm btn-primary" wire:click="addRow">
            <i data-feather='plus'></i> {{ __('dashboard.add') }}
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.name-ar') }}</th>
                    <th>{{ __('dashboard.name-en') }}</th>
                    <th>{{ __('dashboard.shipping-price') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $index => $row)
                    <tr wire:key="governorate-row-{{ $row['id'] ?? 'new-' . $index }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <input type="text" class="form-control" wire:model="rows.{{ $index }}.name_ar">
                            @include('dashboard.includes.error', ['property' => "rows.$index.name_ar"])
                        </td>
                        <td>
                            <input type="text" class="form-control" wire:model="rows.{{ $index }}.name_en">
                            @include('dashboard.includes.error', ['property' => "rows.$index.name_en"])
                        </td>
                        <td style="min-width: 140px">
                            <input type="number" step="0.01" min="0" class="form-control"
                                wire:model="rows.{{ $index }}.shipping_price">
                            @include('dashboard.includes.error', ['property' => "rows.$index.shipping_price"])
                        </td>
                        <td style="min-width: 120px">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" wire:model.live="rows.{{ $index }}.status">
                            </div>
                        </td>
                        <td style="min-width: 140px">
                            <button type="button" class="btn btn-sm btn-danger" title="{{ __('dashboard.delete') }}"
                                wire:click="removeRow({{ $index }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-muted text-center">{{ __('dashboard.no-data') }}</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-success" wire:click="save">
            <span wire:loading.remove wire:target="save">{{ __('dashboard.save') }}</span>
            <span wire:loading wire:target="save">{{ __('dashboard.saving') }}</span>
        </button>
    </div>

</x-show-modal>
