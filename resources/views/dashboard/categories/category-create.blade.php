<x-create-modal title="{{ __('dashboard.create-category') }}">

    <div class="row">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.category-name-ar') }}</label>
            <input type="text" wire:model="name_ar" placeholder="{{ __('dashboard.category-name-ar') }}"
                class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_ar'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.category-name-en') }}</label>
            <input type="text" wire:model="name_en" placeholder="{{ __('dashboard.category-name-en') }}"
                class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_en'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.parent-category') }}</label>
            <select class="form-select" wire:model="parent_id">
                <option value="">{{ __('dashboard.main-category') }}</option>
                @foreach ($parentOptions as $option)
                    <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
            @include('dashboard.includes.error', ['property' => 'parent_id'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.sort-order') }}</label>
            <input type="number" min="0" wire:model="sort_order" placeholder="{{ __('dashboard.sort-order') }}"
                class="form-control">
            @include('dashboard.includes.error', ['property' => 'sort_order'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.color') }}</label>
            <input type="color" wire:model="color" class="form-control form-control-color w-100">
            @include('dashboard.includes.error', ['property' => 'color'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.category-image') }}</label>
            <input type="file" wire:model="image" class="form-control">
            @include('dashboard.includes.error', ['property' => 'image'])
        </div>
    </div>

    @if ($image)
        <div class="row mt-1">
            <div class="col-md-6">
                <img src="{{ $image->temporaryUrl() }}" alt="{{ __('dashboard.category-image') }}"
                    class="rounded border" width="120">
            </div>
        </div>
    @endif

    <div class="row mt-2">
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