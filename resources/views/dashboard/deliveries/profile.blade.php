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
            <div class="col-xl-3 col-lg-5 col-md-5 order-1 order-md-0">
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
            <div class="col-xl-9 col-lg-7 col-md-7 order-0 order-md-1">

                <div class="card mb-1">
                    <div class="card-body">
                        <div class="row g-1 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">{{ __('dashboard.date') }}</label>
                                <input type="date" id="filter_from_date" class="form-control"
                                       value="{{ request('from_date', now()->toDateString()) }}">
                            </div>

                            <div class="col-md-2">
                                <button type="button" id="reset_filter" class="btn btn-outline-secondary w-100">
                                    {{ __('dashboard.reset') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="delivery-report-wrapper">
                    @include('dashboard.partials.delivery-report', [
                    'statistics' => $statistics,
                    'orders' => $orders,
                    'paymentStatusClasses' => $paymentStatusClasses,
                    'orderStatusClasses' => $orderStatusClasses,
                    ])
                </div>
            </div>

        </div>
    </section>



<script>
    console.log('hhh');
    document.addEventListener('DOMContentLoaded', function () {
        const fromInput = document.getElementById('filter_from_date');
        const resetBtn = document.getElementById('reset_filter');
        const wrapper = document.getElementById('delivery-report-wrapper');

        let timeout = null;


        function loadFilteredData() {
            const fromDate = fromInput.value;
            const deliveryId = "{{ $user->id }}";
            const url = new URL(`/ar/dashboard/delivery/profile/${deliveryId}`, window.location.origin);
            url.searchParams.set('from_date', fromDate);

            wrapper.style.opacity = '0.6';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            })
                .then(response => response.text())
                .then(html => {
                    wrapper.innerHTML = html;
                    wrapper.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Filter error:', error);
                    wrapper.style.opacity = '1';
                });
        }

        function delayedLoad() {
            clearTimeout(timeout);
            timeout = setTimeout(loadFilteredData, 300);
        }

        fromInput.addEventListener('change', delayedLoad);

        resetBtn.addEventListener('click', function () {
            const today = "{{ now()->toDateString() }}";
            fromInput.value = today;
            loadFilteredData();
        });
    });
</script>
@endsection
