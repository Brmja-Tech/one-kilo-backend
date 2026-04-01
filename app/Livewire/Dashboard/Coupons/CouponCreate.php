<?php

namespace App\Livewire\Dashboard\Coupons;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CouponCreate extends Component
{
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

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('coupons', 'code')],
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

    public function submit(): void
    {
        $this->code = Str::upper(trim($this->code));
        $this->validate();

        if (! $this->passesBusinessRules()) {
            return;
        }

        Coupon::query()->create($this->payload());

        $this->resetForm();
        $this->resetValidation();

        $this->dispatch('notify', type: 'success', message: __('dashboard.coupon-add-successfully'));
        $this->dispatch('createModalToggle');
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

    private function passesBusinessRules(): bool
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

    private function resetForm(): void
    {
        $this->reset([
            'code',
            'type',
            'value',
            'min_order_amount',
            'max_discount_amount',
            'usage_limit',
            'usage_limit_per_user',
            'starts_at',
            'expires_at',
            'status',
        ]);

        $this->type = 'amount';
        $this->status = true;
    }

    public function render()
    {
        return view('dashboard.coupons.coupon-create');
    }
}
