<x-create-modal title="{{ __('dashboard.create-notification') }}">

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.title-ar') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='title_ar' placeholder="{{ __('dashboard.title-ar') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'title_ar'])
        </div>
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.title-en') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='title_en' placeholder="{{ __('dashboard.title-en') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'title_en'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.message-ar') }}</label>
            </div>
            <div class="form-group">
                <textarea wire:model='message_ar' class="form-control" id="" cols="30" rows="3"></textarea>
            </div>
            @include('dashboard.includes.error', ['property' => 'message_ar'])
        </div>
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.message-en') }}</label>
            </div>
            <div class="form-group">
                <textarea wire:model='message_en' class="form-control" id="" cols="30" rows="3"></textarea>
            </div>
            @include('dashboard.includes.error', ['property' => 'message_en'])
        </div>
    </div>


</x-create-modal>
