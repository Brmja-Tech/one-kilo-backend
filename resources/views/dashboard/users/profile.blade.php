@extends('dashboard.master', ['title' => __('dashboard.user-profile')])
@section('users-active', 'active')
@section('users-open', 'open')

@section('content')
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

        $transactionTypeClasses = [
            \App\Models\WalletTransaction::TYPE_CREDIT => 'success',
            \App\Models\WalletTransaction::TYPE_DEBIT => 'danger',
        ];

        $userImage = asset($user->image ?: 'uploads/images/image.png');
    @endphp

    <section class="app-user-view-account">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mt-2 mb-2" src="{{ $userImage }}" height="110"
                                    width="110" alt="{{ $user->name }}" />
                                <div class="user-info text-center">
                                    <h4 class="mb-50">{{ $user->name }}</h4>
                                    <span class="badge bg-light-secondary">#{{ $user->id }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-50 justify-content-center my-2">
                            <span class="badge bg-light-{{ $user->status ? 'success' : 'danger' }}">
                                {{ $user->status ? __('dashboard.active') : __('dashboard.inactive') }}
                            </span>
                            <span class="badge bg-light-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                                {{ $user->email_verified_at ? __('dashboard.verified') : __('dashboard.not-verified') }}
                            </span>
                        </div>

                        <h4 class="fw-bolder border-bottom pb-50 mb-1">{{ __('dashboard.details-for') }} {{ $user->name }}</h4>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.name') }}:</span>
                                <span>{{ $user->name }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.email') }}:</span>
                                <span>{{ $user->email ?: '-' }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.phone') }}:</span>
                                <span>{{ $user->phone ?: '-' }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.country') }}:</span>
                                <span>{{ $user->country?->name ?? '-' }}</span>
                            </li>
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.governorate') }}:</span>
                                <span>{{ $user->governorate?->name ?? '-' }}</span>
                            </li>
                            <li>
                                <span class="fw-bolder me-25">{{ __('dashboard.member-since') }}:</span>
                                <span>{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.wallet-summary') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($wallet)
                            <ul class="list-unstyled mb-0">
                                <li class="mb-75 d-flex justify-content-between">
                                    <span>{{ __('dashboard.wallet-balance') }}</span>
                                    <span class="fw-bolder">{{ number_format((float) $wallet->balance, 2) }}</span>
                                </li>
                                <li class="mb-75 d-flex justify-content-between">
                                    <span>{{ __('dashboard.wallet-status') }}</span>
                                    <span class="badge bg-light-{{ $wallet->status ? 'success' : 'danger' }}">
                                        {{ $wallet->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span>{{ __('dashboard.wallet-created-at') }}</span>
                                    <span>{{ $wallet->created_at?->format('Y-m-d') ?? '-' }}</span>
                                </li>
                            </ul>
                        @else
                            <p class="text-muted mb-0">{{ __('dashboard.no-wallet-found') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="row g-1 mb-1">
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.total-orders') }}</small>
                                <h3 class="mb-0">{{ $user->orders_count }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.favorites-count') }}</small>
                                <h3 class="mb-0">{{ $user->favorites_count }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.wallet-balance') }}</small>
                                <h3 class="mb-0">{{ number_format((float) ($wallet?->balance ?? 0), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.wallet-transactions-count') }}</small>
                                <h3 class="mb-0">{{ $user->wallet_transactions_count }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.recent-wallet-transactions') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('dashboard.transaction-direction') }}</th>
                                    <th>{{ __('dashboard.transaction-type') }}</th>
                                    <th>{{ __('dashboard.amount') }}</th>
                                    <th>{{ __('dashboard.balance-before') }}</th>
                                    <th>{{ __('dashboard.balance-after') }}</th>
                                    <th>{{ __('dashboard.reference') }}</th>
                                    <th>{{ __('dashboard.linked-order') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                    <th>{{ __('dashboard.created-at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($walletTransactions as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->id }}</td>
                                        <td>
                                            <span class="badge bg-light-{{ $transactionTypeClasses[$transaction->type] ?? 'secondary' }}">
                                                {{ __('dashboard.transaction-direction-' . $transaction->type) }}
                                            </span>
                                        </td>
                                        <td>{{ __('dashboard.wallet-transaction-type-' . str_replace('_', '-', $transaction->transaction_type)) }}</td>
                                        <td>{{ number_format((float) $transaction->amount, 2) }}</td>
                                        <td>{{ number_format((float) $transaction->balance_before, 2) }}</td>
                                        <td>{{ number_format((float) $transaction->balance_after, 2) }}</td>
                                        <td>{{ $transaction->reference ?: '-' }}</td>
                                        <td>
                                            @if ($transaction->order)
                                                <a href="{{ route('dashboard.orders.show', $transaction->order) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ $transaction->order->order_number }}
                                                </a>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light-{{ $transaction->status ? 'success' : 'danger' }}">
                                                {{ $transaction->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-2">{{ __('dashboard.no-wallet-transactions') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.user-orders') }}</h4>
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

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.favorite-products') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.image') }}</th>
                                    <th>{{ __('dashboard.product') }}</th>
                                    <th>{{ __('dashboard.sku') }}</th>
                                    <th>{{ __('dashboard.price') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($favorites as $favorite)
                                    <tr>
                                        <td>
                                            @if ($favorite->product?->image)
                                                <img src="{{ asset($favorite->product->image) }}" alt="{{ $favorite->product->name }}"
                                                    class="rounded border object-fit-cover" width="52" height="52">
                                            @else
                                                <span class="text-muted">{{ __('dashboard.no-image') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($favorite->product)
                                                <div class="fw-semibold">{{ $favorite->product->name }}</div>
                                                <small class="text-muted">{{ $favorite->product->slug }}</small>
                                            @else
                                                <span class="text-muted">{{ __('dashboard.product-not-available') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $favorite->product?->sku ?: '-' }}</td>
                                        <td>
                                            @if ($favorite->product)
                                                {{ number_format((float) $favorite->product->priceAfterDiscount(), 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($favorite->product)
                                                <span class="badge bg-light-{{ $favorite->product->status ? 'success' : 'danger' }}">
                                                    {{ $favorite->product->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                                </span>
                                            @else
                                                <span class="badge bg-light-secondary">{{ __('dashboard.inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-2">{{ __('dashboard.no-favorite-products') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
