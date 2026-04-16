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
                                    <h4 class="mb-50">{{ $user->full_name }}</h4>
                                    <span class="badge bg-light-secondary">#{{ $user->id }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-50 justify-content-center my-2">
                            <span class="badge bg-light-{{ $user->status ? 'success' : 'danger' }}">
                                {{ $user->login_status ? __('dashboard.active') : __('dashboard.inactive') }}
                            </span>
                            <span class="badge bg-light-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                                {{ $user->email_verified_at ? __('dashboard.verified') : __('dashboard.not-verified') }}
                            </span>
                            <span class="badge
    {{ $user->status == 'approved' ? 'bg-success' : '' }}
    {{ $user->status == 'pending' ? 'bg-warning' : '' }}
    {{ $user->status == 'rejected' ? 'bg-danger' : '' }}">

    {{
        $user->status == 'approved' ? __('Approved') :
        ($user->status == 'pending' ? __('Pending') : __('Rejected'))
    }}
</span>
                        </div>




                        <h4 class="fw-bolder border-bottom pb-50 mb-1">{{ __('dashboard.details-for') }} {{ $user->full_name }}</h4>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-75">
                                <span class="fw-bolder me-25">{{ __('dashboard.name') }}:</span>
                                <span>{{ $user->full_name }}</span>
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
                                <span class="fw-bolder me-25">{{ __('dashboard.vehicle_type') }}:</span>
                                <span>{{ $user->vehicle_type ?? '-' }}</span>
                            </li>

                            <li>
                                <span class="fw-bolder me-25">{{ __('dashboard.member-since') }}:</span>
                                <span>{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
                            </li>



                        </ul>
                    </div>
                </div>

            </div>

            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="row g-1 mb-1">
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.total-active-orders') }}</small>
                                <h3 class="mb-0">{{ $user->confirmed_orders_count }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 mb-0">
                            <div class="card-body">
                                <small class="text-muted d-block">{{ __('dashboard.total-delivered-orders') }}</small>
                                <h3 class="mb-0">{{ $user->delivered_orders_count }}</h3>
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
            </div>
        </div>
    </section>
@endsection
