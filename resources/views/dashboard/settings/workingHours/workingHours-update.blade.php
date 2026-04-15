<x-update-modal title="{{ __('dashboard.update-workingHours') }}">
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.day_name-ar') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='day_name_ar' placeholder="{{ __('dashboard.day_name-ar') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'day_name_ar'])
        </div>
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.day_name-en') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='day_name_en' placeholder="{{ __('dashboard.day_name-en') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'day_name_en'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.open_time') }}</label>
            </div>
            <div class="form-group">
                <input type="time" wire:model="open_time" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'open_time'])
        </div>
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.close_time') }}</label>
            </div>
            <div class="form-group">
                <input type="time" wire:model="close_time" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'close_time'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.status') }}</label>
            </div>
            <div class="form-group">
                <select wire:model="status" wire:loading.attr="disabled" class="form-control" wire:target="status">
                    <option value="open" selected>{{ __('dashboard.open') }}</option>
                    <option value="close">{{ __('dashboard.close') }}</option>
                    <option value="busy">{{ __('dashboard.busy') }}</option>
                </select>
            </div>
            @include('dashboard.includes.error', ['property' => 'status'])
        </div>
    </div>
</x-update-modal>
