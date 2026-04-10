<x-create-modal title="{{ __('dashboard.create-region') }}">

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

    <div class="row mt-2">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.shipping-price') }}</label>
            <input type="number" step="0.01" min="0" wire:model="shipping_price"
                placeholder="{{ __('dashboard.shipping-price') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'shipping_price'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label d-block">{{ __('dashboard.status') }}</label>
            <div class="d-flex align-items-center gap-2">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model.live="status">
                </div>
                <span class="fw-semibold">
                    {{ $status ? __('dashboard.active') : __('dashboard.inactive') }}
                </span>
            </div>
            @include('dashboard.includes.error', ['property' => 'status'])
        </div>
    </div>

</x-create-modal>

