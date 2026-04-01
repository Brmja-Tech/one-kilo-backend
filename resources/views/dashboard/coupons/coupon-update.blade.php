<x-update-modal title="{{ __('dashboard.update-coupon') }}">

    <div class="row">
        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.code') }}</label>
            <input type="text" wire:model="code" placeholder="{{ __('dashboard.code') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'code'])
        </div>

        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.type') }}</label>
            <select class="form-select" wire:model.live="type">
                <option value="amount">{{ __('dashboard.amount') }}</option>
                <option value="percentage">{{ __('dashboard.percentage') }}</option>
            </select>
            @include('dashboard.includes.error', ['property' => 'type'])
        </div>

        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.value') }}</label>
            <input type="number" step="0.01" min="0" wire:model="value" class="form-control"
                placeholder="{{ __('dashboard.value') }}">
            @include('dashboard.includes.error', ['property' => 'value'])
        </div>

        <div class="col-md-6 mt-1">
            <label class="col-form-label">{{ __('dashboard.minimum-order-amount') }}</label>
            <input type="number" step="0.01" min="0" wire:model="min_order_amount" class="form-control"
                placeholder="{{ __('dashboard.minimum-order-amount') }}">
            @include('dashboard.includes.error', ['property' => 'min_order_amount'])
        </div>

        <div class="col-md-6 mt-1">
            <label class="col-form-label">{{ __('dashboard.maximum-discount-amount') }}</label>
            <input type="number" step="0.01" min="0" wire:model="max_discount_amount" class="form-control"
                placeholder="{{ __('dashboard.maximum-discount-amount') }}" @disabled($type !== 'percentage')>
            <small class="text-muted">{{ __('dashboard.optional-for-percentage-coupons') }}</small>
            @include('dashboard.includes.error', ['property' => 'max_discount_amount'])
        </div>

        <div class="col-md-4 mt-1">
            <label class="col-form-label">{{ __('dashboard.usage-limit') }}</label>
            <input type="number" min="1" wire:model="usage_limit" class="form-control"
                placeholder="{{ __('dashboard.usage-limit') }}">
            @include('dashboard.includes.error', ['property' => 'usage_limit'])
        </div>

        <div class="col-md-4 mt-1">
            <label class="col-form-label">{{ __('dashboard.usage-limit-per-user') }}</label>
            <input type="number" min="1" wire:model="usage_limit_per_user" class="form-control"
                placeholder="{{ __('dashboard.usage-limit-per-user') }}">
            @include('dashboard.includes.error', ['property' => 'usage_limit_per_user'])
        </div>

        <div class="col-md-4 mt-1">
            <label class="col-form-label">{{ __('dashboard.used-count') }}</label>
            <input type="number" class="form-control" value="{{ $used_count }}" disabled>
        </div>

        <div class="col-md-6 mt-1">
            <label class="col-form-label">{{ __('dashboard.starts-at') }}</label>
            <input type="datetime-local" wire:model="starts_at" class="form-control">
            @include('dashboard.includes.error', ['property' => 'starts_at'])
        </div>

        <div class="col-md-6 mt-1">
            <label class="col-form-label">{{ __('dashboard.expires-at') }}</label>
            <input type="datetime-local" wire:model="expires_at" class="form-control">
            @include('dashboard.includes.error', ['property' => 'expires_at'])
        </div>

        <div class="col-md-6 mt-1">
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

</x-update-modal>
