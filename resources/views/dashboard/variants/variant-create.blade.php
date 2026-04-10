<x-create-modal title="{{ __('dashboard.create-variant') }}">
    <div class="row">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.name-ar') }}</label>
            <input type="text" wire:model="name_ar" placeholder="{{ __('dashboard.name-ar') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_ar'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.name-en') }}</label>
            <input type="text" wire:model="name_en" placeholder="{{ __('dashboard.name-en') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_en'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.key') }}</label>
            <input type="text" wire:model="key" placeholder="{{ __('dashboard.key') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'key'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.sort-order') }}</label>
            <input type="number" wire:model="sort_order" placeholder="{{ __('dashboard.sort-order') }}"
                class="form-control" min="0">
            @include('dashboard.includes.error', ['property' => 'sort_order'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label d-block">{{ __('dashboard.status') }}</label>
            <div class="d-flex align-items-center gap-2 pt-50">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model="status">
                </div>
                <span class="fw-semibold">{{ $status ? __('dashboard.active') : __('dashboard.inactive') }}</span>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="col-form-label m-0">{{ __('dashboard.items') }}</label>
                <button type="button" class="btn btn-sm btn-info" wire:click="addItem">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('dashboard.add-item') }}
                </button>
            </div>

            @if ($items)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.item-ar') }}</th>
                                <th>{{ __('dashboard.item-en') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.sort-order') }}</th>
                                <th class="text-end">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model="items.{{ $index }}.name_ar"
                                            placeholder="{{ __('dashboard.item-ar') }}"
                                            class="form-control form-control-sm">
                                        @error("items.{$index}.name_ar")
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" wire:model="items.{{ $index }}.name_en"
                                            placeholder="{{ __('dashboard.item-en') }}"
                                            class="form-control form-control-sm">
                                        @error("items.{$index}.name_en")
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:model="items.{{ $index }}.status">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" wire:model="items.{{ $index }}.sort_order"
                                            class="form-control form-control-sm" min="0">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            wire:click="removeItem({{ $index }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @include('dashboard.includes.error', ['property' => 'items'])
        </div>
    </div>
</x-create-modal>
