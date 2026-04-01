<x-update-modal title="{{ __('dashboard.update-faq') }}">
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.question-ar') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='question_ar' placeholder="{{ __('dashboard.question-ar') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'question_ar'])
        </div>
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.question-en') }}</label>
            </div>
            <div class="form-group">
                <input type="text" wire:model='question_en' placeholder="{{ __('dashboard.question-en') }}"
                    class="form-control">
            </div>
            @include('dashboard.includes.error', ['property' => 'question_en'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.answer-ar') }}</label>
            </div>
            <div class="form-group">
                <textarea wire:model='answer_ar' class="form-control" id="" cols="30" rows="3"></textarea>
            </div>
            @include('dashboard.includes.error', ['property' => 'answer_ar'])
        </div>
        <div class="col-md-12">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.answer-en') }}</label>
            </div>
            <div class="form-group">
                <textarea wire:model='answer_en' class="form-control" id="" cols="30" rows="3"></textarea>
            </div>
            @include('dashboard.includes.error', ['property' => 'answer_en'])
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-sm-6">
                <label class="col-form-label">{{ __('dashboard.status') }}</label>
            </div>
            <div class="form-group">
                <select wire:model="status" wire:loading.attr="disabled" class="form-control" wire:target="status">
                    <option selected>{{ __('dashboard.select-status') }}</option>
                    <option value="1">{{ __('dashboard.active') }}</option>
                    <option value="0">{{ __('dashboard.inactive') }}</option>
                </select>
            </div>
            @include('dashboard.includes.error', ['property' => 'status'])
        </div>
    </div>
</x-update-modal>
