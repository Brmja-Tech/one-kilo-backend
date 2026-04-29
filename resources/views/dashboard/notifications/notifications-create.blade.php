<x-create-modal title="{{ __('dashboard.create-notification') }}">

    <div class="row mt-4">
        <div class="col-md-12">
            <label class="form-label">{{ __('dashboard.send_to') }}</label>
            <div class="d-flex gap-2 mt-1">
                <div class="form-check">
                    <input class="form-check-input" type="radio" wire:model.live="type" value="all" id="typeAll">
                    <label class="form-check-label" for="typeAll">{{ __('dashboard.all_users') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" wire:model.live="type" value="specific"
                        id="typeSpecific">
                    <label class="form-check-label" for="typeSpecific">{{ __('dashboard.specific_users') }}</label>
                </div>
            </div>
            @include('dashboard.includes.error', ['property' => 'type'])
        </div>
    </div>

    @if ($type == 'specific')
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="{{ __('dashboard.search_users') }}...">
                </div>

                <div class="border rounded p-1" style="max-height: 200px; overflow-y: auto;">
                    @forelse($users as $user)
                        <div class="form-check mb-50">
                            <input class="form-check-input" type="checkbox" wire:model="selected_users"
                                value="{{ $user->id }}" id="user{{ $user->id }}">
                            <label class="form-check-label" for="user{{ $user->id }}">
                                {{ $user->name }} ({{ $user->phone }})
                            </label>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('dashboard.no-data') }}</p>
                    @endforelse
                </div>
                @include('dashboard.includes.error', ['property' => 'selected_users'])
            </div>
        </div>
    @endif

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
