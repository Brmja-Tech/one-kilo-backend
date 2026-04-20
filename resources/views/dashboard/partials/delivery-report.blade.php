<div class="row g-1 mb-1">

    <!-- Cash -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 mb-0">
            <div class="card-body">
                <h4 class="text-muted d-block">{{ __('dashboard.cash_orders') }}</h4>
                <h6 class="mb-1">{{ __('dashboard.orders') }}: {{ $statistics->cash_orders_count }}</h6>
                <h6 class="mb-1">{{ __('dashboard.total_price') }}: {{ $statistics->cash_total_amount }}</h6>
                <h6 class="mb-0">{{ __('dashboard.delivery_fee') }}: {{ $statistics->cash_delivery_fee }}</h6>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 mb-0">
            <div class="card-body">
                <h4 class="text-muted d-block">{{ __('dashboard.card_orders') }}</h4>
                <h6 class="mb-1">{{ __('dashboard.orders') }}: {{ $statistics->card_orders_count }}</h6>
                <h6 class="mb-1">{{ __('dashboard.total_price') }}: {{ $statistics->card_total_amount }}</h6>
                <h6 class="mb-0">{{ __('dashboard.delivery_fee') }}: {{ $statistics->card_delivery_fee }}</h6>
            </div>
        </div>
    </div>

    <!-- Total -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 mb-0">
            <div class="card-body">
                <h4 class="text-muted d-block">{{ __('dashboard.total_orders') }}</h4>
                <h6 class="mb-1">{{ __('dashboard.orders') }}: {{ $statistics->delivered_orders_count }}</h6>
                <h6 class="mb-1">{{ __('dashboard.total_price') }}: {{ $statistics->total_orders_amount }}</h6>
                <h6 class="mb-0">{{ __('dashboard.delivery_fee') }}: {{ $statistics->total_delivery_fee }}</h6>
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ __('dashboard.delivery-orders') }}</h4>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>{{ __('dashboard.order-number') }}</th>
                <th>{{ __('dashboard.created-at') }}</th>
                <th>{{ __('dashboard.total') }}</th>
                <th>{{ __('dashboard.payment-method') }}</th>
                <th>{{ __('dashboard.payment-status') }}</th>
                <th>{{ __('dashboard.order-status') }}</th>
                <th>{{ __('dashboard.item-count') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>
                    <div class="fw-semibold">{{ $order->order_number }}</div>
                    <small class="text-muted">#{{ $order->id }}</small>
                </td>
                <td>{{ $order->placed_at?->format('Y-m-d H:i') ?? $order->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                <td>{{ number_format((float) $order->total, 2) }}</td>
                <td>
                            <span class="badge bg-light-primary">
                                {{ __('dashboard.payment-method-' . str_replace('_', '-', $order->payment_method)) }}
                            </span>
                </td>
                <td>
                            <span class="badge bg-light-{{ $paymentStatusClasses[$order->payment_status] ?? 'secondary' }}">
                                {{ __('dashboard.payment-status-' . str_replace('_', '-', $order->payment_status)) }}
                            </span>
                </td>
                <td>
                            <span class="badge bg-light-{{ $orderStatusClasses[$order->status] ?? 'secondary' }}">
                                {{ __('dashboard.order-status-' . str_replace('_', '-', $order->status)) }}
                            </span>
                </td>
                <td>{{ (int) ($order->items_quantity_sum ?? $order->items_count) }}</td>
                <td>
                    <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                        {{ __('dashboard.open-order-details') }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-2">{{ __('dashboard.no-orders-found') }}</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
