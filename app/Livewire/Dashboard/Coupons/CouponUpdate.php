<?php

namespace App\Livewire\Dashboard\Coupons;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CouponUpdate extends Component
{
    public ?int $couponId = null;

    public string $code = '';

    public string $type = 'amount';

    public string $value = '';

    public string $min_order_amount = '';

    public string $max_discount_amount = '';

    public string $usage_limit = '';

    public string $usage_limit_per_user = '';

    public string $starts_at = '';

    public string $expires_at = '';

    public bool $status = true;

    public int $used_count = 0;

    protected $listeners = [
        'couponUpdate' => 'loadItem',
    ];

    public function rules(): array
    {
        $id = $this->couponId ?? 0;

        return [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('coupons', 'code')->ignore($id)],
            'type' => ['required', Rule::in(['amount', 'percentage'])],
            'value' => ['required', 'numeric', 'gt:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'gt:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function loadItem(int $id): void
    {
        $item = Coupon::query()->find($id);

        if (! $item) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->couponId = $item->id;
        $this->code = $item->code;
        $this->type = $item->type;
        $this->value = (string) $item->value;
        $this->min_order_amount = $item->min_order_amount !== null ? (string) $item->min_order_amount : '';
        $this->max_discount_amount = $item->max_discount_amount !== null ? (string) $item->max_discount_amount : '';
        $this->usage_limit = $item->usage_limit !== null ? (string) $item->usage_limit : '';
        $this->usage_limit_per_user = $item->usage_limit_per_user !== null ? (string) $item->usage_limit_per_user : '';
        $this->starts_at = optional($item->starts_at)->format('Y-m-d\TH:i');
        $this->expires_at = optional($item->expires_at)->format('Y-m-d\TH:i');
        $this->status = (bool) $item->status;
        $this->used_count = (int) $item->used_count;
        $this->resetValidation();

        $this->dispatch('updateModalToggle');
    }

    public function submit(): void
    {
        $coupon = Coupon::query()->find($this->couponId);

        if (! $coupon) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->code = Str::upper(trim($this->code));
        $this->validate();

        if (! $this->passesBusinessRules($coupon)) {
            return;
        }

        $coupon->update($this->payload());

        $this->dispatch('notify', type: 'success', message: __('dashboard.coupon-update-successfully'));
        $this->dispatch('updateModalToggle');
        $this->dispatch('refreshData')->to(CouponsData::class);
    }

    private function payload(): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
            'value' => round((float) $this->value, 2),
            'min_order_amount' => $this->nullableFloat($this->min_order_amount),
            'max_discount_amount' => $this->type === 'percentage'
                ? $this->nullableFloat($this->max_discount_amount)
                : null,
            'usage_limit' => $this->nullableInt($this->usage_limit),
            'usage_limit_per_user' => $this->nullableInt($this->usage_limit_per_user),
            'starts_at' => $this->nullableDateTime($this->starts_at),
            'expires_at' => $this->nullableDateTime($this->expires_at),
            'status' => $this->status,
        ];
    }

    private function passesBusinessRules(Coupon $coupon): bool
    {
        if ($this->type === 'percentage' && (float) $this->value > 100) {
            $this->addError(
                'value',
                __('validation.max.numeric', ['attribute' => __('dashboard.percentage'), 'max' => 100])
            );

            return false;
        }

        $usageLimit = $this->nullableInt($this->usage_limit);
        $usageLimitPerUser = $this->nullableInt($this->usage_limit_per_user);

        if ($usageLimit !== null && $usageLimit < (int) $coupon->used_count) {
            $this->addError('usage_limit', __('dashboard.usage-limit-cannot-be-less-than-used-count'));

            return false;
        }

        if ($usageLimit !== null && $usageLimitPerUser !== null && $usageLimitPerUser > $usageLimit) {
            $this->addError('usage_limit_per_user', __('dashboard.per-user-limit-cannot-exceed-usage-limit'));

            return false;
        }

        return true;
    }

    private function nullableFloat(?string $value): ?float
    {
        $value = $this->normalizeNullableValue($value);

        return $value === null ? null : round((float) $value, 2);
    }

    private function nullableInt(?string $value): ?int
    {
        $value = $this->normalizeNullableValue($value);

        return $value === null ? null : (int) $value;
    }

    private function nullableDateTime(?string $value): ?string
    {
        $value = $this->normalizeNullableValue($value);

        return $value === null ? null : Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    private function normalizeNullableValue(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function render()
    {
        return view('dashboard.coupons.coupon-update');
    }
}
