<div>
    @php
        $orderStatusClasses = [
            \App\Models\Order::STATUS_PENDING => 'warning',
            \App\Models\Order::STATUS_AWAITING_PAYMENT => 'info',
            \App\Models\Order::STATUS_CONFIRMED => 'primary',
            \App\Models\Order::STATUS_PREPARING => 'secondary',
            \App\Models\Order::STATUS_OUT_FOR_DELIVERY => 'info',
            \App\Models\Order::STATUS_DELIVERED => 'success',
            \App\Models\Order::STATUS_CANCELED => 'danger',
            \App\Models\Order::STATUS_FAILED => 'danger',
        ];

        $paymentStatusClasses = [
            \App\Models\Order::PAYMENT_STATUS_UNPAID => 'danger',
            \App\Models\Order::PAYMENT_STATUS_PENDING => 'warning',
            \App\Models\Order::PAYMENT_STATUS_PAID => 'success',
            \App\Models\Order::PAYMENT_STATUS_FAILED => 'danger',
            \App\Models\Order::PAYMENT_STATUS_REFUNDED => 'secondary',
        ];
    @endphp

    <div class="border rounded p-1 mb-1">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <h5 class="mb-0">{{ __('dashboard.filters') }}</h5>
        </div>

        <div class="row g-1">
            <div class="col-md-4">
                <input type="text" class="form-control" wire:model.live="search"
                    placeholder="{{ __('dashboard.search') }} (#ID / {{ __('dashboard.order-number') }} / {{ __('dashboard.customer') }})">
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="statusFilter">
                    <option value="all">{{ __('dashboard.all-statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __('dashboard.order-status-' . str_replace('_', '-', $status)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="paymentStatusFilter">
                    <option value="all">{{ __('dashboard.all-payment-statuses') }}</option>
                    @foreach ($paymentStatuses as $paymentStatus)
                        <option value="{{ $paymentStatus }}">
                            {{ __('dashboard.payment-status-' . str_replace('_', '-', $paymentStatus)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="paymentMethodFilter">
                    <option value="all">{{ __('dashboard.all-payment-methods') }}</option>
                    @foreach ($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod }}">
                            {{ __('dashboard.payment-method-' . str_replace('_', '-', $paymentMethod)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="customerFilter">
                    <option value="all">{{ __('dashboard.all-customers') }}</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer['id'] }}">{{ $customer['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="dateFrom"
                    title="{{ __('dashboard.from-date') }}">
            </div>

            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="dateTo"
                    title="{{ __('dashboard.to-date') }}">
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="countryFilter">
                    <option value="all">{{ __('dashboard.all-countries') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country['id'] }}">{{ $country['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select class="form-select" wire:model.live="governorateFilter">
                    <option value="all">{{ __('dashboard.all-governorates') }}</option>
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate['id'] }}">{{ $governorate['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" min="0" step="0.01" class="form-control" wire:model.live="totalMin"
                    placeholder="{{ __('dashboard.min-total') }}">
            </div>

            <div class="col-md-1">
                <input type="number" min="0" step="0.01" class="form-control" wire:model.live="totalMax"
                    placeholder="{{ __('dashboard.max-total') }}">
            </div>

            <div class="col-md-1">
                <select class="form-select" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.order-number') }}</th>
                    <th>{{ __('dashboard.customer') }}</th>
                    <th>{{ __('dashboard.phone') }}</th>
                    <th>{{ __('dashboard.total') }}</th>
                    <th>{{ __('dashboard.payment-method') }}</th>
                    <th>{{ __('dashboard.payment-status') }}</th>
                    <th>{{ __('dashboard.order-status') }}</th>
                    <th>{{ __('dashboard.item-count') }}</th>
                    <th>{{ __('dashboard.placed-at') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $index => $item)
                    @php
                        $addressSnapshot = $item->addressSnapshot();
                        $customerPhone = $item->user?->phone
                            ?? data_get($addressSnapshot, 'phone')
                            ?? $item->address?->phone;
                    @endphp
                    <tr>
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->order_number }}</div>
                            <small class="text-muted">#{{ $item->id }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $item->user?->name ?? data_get($addressSnapshot, 'contact_name') ?? $item->address?->contact_name ?? '-' }}</div>
                            <small class="text-muted">{{ $item->user?->email ?? data_get($addressSnapshot, 'city') ?? $item->address?->city ?? '-' }}</small>
                        </td>
                        <td>{{ $customerPhone ?? '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ number_format((float) $item->total, 2) }}</div>
                            <small class="text-muted">
                                {{ __('dashboard.subtotal') }}: {{ number_format((float) $item->subtotal, 2) }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light-primary">
                                {{ __('dashboard.payment-method-' . str_replace('_', '-', $item->payment_method)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light-{{ $paymentStatusClasses[$item->payment_status] ?? 'secondary' }}">
                                {{ __('dashboard.payment-status-' . str_replace('_', '-', $item->payment_status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light-{{ $orderStatusClasses[$item->status] ?? 'secondary' }}">
                                {{ __('dashboard.order-status-' . str_replace('_', '-', $item->status)) }}
                            </span>
                        </td>
                        <td>
                            <div>{{ (int) ($item->items_quantity_sum ?? 0) }}</div>
                            <small class="text-muted">{{ __('dashboard.items') }}: {{ $item->items_count }}</small>
                        </td>
                        <td>{{ $item->placed_at?->format('Y-m-d H:i') ?? $item->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('dashboard.orders.show', $item) }}" class="btn btn-sm btn-primary"
                                title="{{ __('dashboard.view-order') }}">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-2">{{ __('dashboard.no-orders-found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>
</div>