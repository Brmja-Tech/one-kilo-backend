<x-create-modal title="{{ __('dashboard.create-delivery') }}">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" width="150" class="wd-80 ">
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="file" wire:model='image' class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'image'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" wire:model='full_name' placeholder="{{ __('dashboard.full_name') }}" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'full_name'])
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="email" wire:model='email' placeholder="{{ __('dashboard.email') }}" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'email'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group">
                <input type="number" wire:model='phone' placeholder="{{ __('dashboard.phone') }}" class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'phone'])
        </div>
    </div>


    <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group">
                <input type="password" class="form-control" wire:model="password"
                    placeholder="{{ __('dashboard.password') }}">
            </div>
            @include('dashboard.includes.error', ['property' => 'password'])
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="password" class="form-control" wire:model="password_confirmation"
                    placeholder="{{ __('dashboard.password-confirmation') }}">
            </div>
            @include('dashboard.includes.error', ['property' => 'password_confirmation'])
        </div>


    </div>
    <div class="row mt-4">
    <div class="col-md-6">
        <div class="form-group">
            <input type="text" wire:model='vehicle_type' placeholder="{{ __('dashboard.vehicle_type') }}" class="form-control">
        </div>
        @include('dashboard.includes.error', ['property' => 'vehicle_type'])
    </div>
    </div>

</x-create-modal>
