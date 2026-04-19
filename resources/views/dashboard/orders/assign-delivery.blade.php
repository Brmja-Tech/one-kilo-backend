
@extends('dashboard.master', ['title' => __('dashboard.order-details')])
@section('orders-active', 'active')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h3 class="mb-0">Assign Delivery To Order #{{ $order->order_number }}</h3>

        <a href="{{ route('dashboard.orders.show', $order->id) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-arrow-left"></i> Back To Order Details
        </a>
    </div>

    <div class="row g-4">
        {{-- Available Deliveries --}}
        <div class="col-lg-6">
            <div class="delivery-card">
                <div class="delivery-card-header">
                    <h4 class="mb-0">Available Deliveries</h4>
                </div>

                <div class="table-scroll">
                    <table class="table delivery-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($availableDeliveries as $delivery)
                        <tr>
                            <td>
                                <img
                                    src="{{ asset($delivery->image ?: 'uploads/images/image.png')}}"
                                    alt="{{ $delivery->full_name }}" style="width: 80px"
                                    class="delivery-avatar">
                            </td>
                            <td>{{ $delivery->full_name }}</td>
                            <td class="text-center">
                                @if($order->delivery_id == $delivery->id)
                                <button type="button" class="btn btn-assigned" disabled>
                                    <i class="fa-solid fa-circle-check"></i> Assigned to this order
                                </button>
                                @else
                                <form action="{{ route('dashboard.orders.assign', $order->id) }}" method="POST" class="assign-form">
                                    @csrf
                                    <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">

                                    <button type="submit" class="btn btn-assign assign-btn" style="background: linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7));color: white">
                                        <i class="fa-solid fa-truck"></i> Assign
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No available deliveries found.
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Busy Deliveries --}}
        <div class="col-lg-6">
            <div class="delivery-card">
                <div class="delivery-card-header">
                    <h4 class="mb-0">Busy Deliveries</h4>
                </div>

                <div class="table-scroll">
                    <table class="table delivery-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Current Orders</th>
                            <th>Addresses</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($busyDeliveries as $delivery)
                        <tr>
                            <td>
                                <img style="width: 80px"
                                    src="{{ asset($delivery->image ?: 'uploads/images/image.png')}}"
                                    alt="{{ $delivery->full_name }}"
                                    class="delivery-avatar">
                            </td>
                            <td>
                                <div>{{ $delivery->full_name }}</div>

                            </td>
                            <td>
                                {{ $delivery->orders->count() }}
                            </td>
                            <td>
                                @if($delivery->orders->count())
                                <div class="address-list">
                                    @foreach($delivery->orders as $busyOrder)
                                    <div class="address-box">
                                        <strong>Order #{{ $busyOrder->id }}</strong>
                                        <div class="small mt-1">
                                            {{ $busyOrder->address->fullAddress() }}
                                        </div>
                                        <div class="tiny-status">
                                            {{ str_replace('_', ' ', $busyOrder->status) }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-muted">No active orders</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($order->delivery_id == $delivery->id)
                                <button type="button" class="btn btn-assigned" disabled>
                                    <i class="fa-solid fa-circle-check"></i> Assigned to this order
                                </button>
                                @else
                                <form action="{{ route('dashboard.orders.assign', $order->id) }}" method="POST" class="assign-form">
                                    @csrf
                                    <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">

                                    <button type="submit" class="btn btn-assign-outline assign-btn">
                                        <i class="fa-solid fa-truck"></i> Assign
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No busy deliveries found.
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .delivery-card {
        background: #fff;
        border: 1px solid #ececf3;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .delivery-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f0f0f5;
        background: #fafafe;
    }

    .delivery-card-header h4 {
        font-size: 24px;
        font-weight: 700;
        color: #5d5877;
    }

    .table-scroll {
        max-height: 520px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .delivery-table {
        min-width: 720px;
        margin-bottom: 0;
    }

    .delivery-table thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 2;
        font-size: 14px;
        font-weight: 700;
        color: #5d5877;
        border-bottom: 1px solid #ececf3;
        white-space: nowrap;
    }

    .delivery-table tbody td {
        vertical-align: middle;
        color: #6b6880;
        font-size: 15px;
        border-color: #f1f1f6;
    }

    .delivery-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f1f1f6;
        background: #f8f8fb;
    }

    .btn-assign,
    .btn-assign-outline {
        min-width: 140px;
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 600;
        font-size: 15px;
    }





    .address-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 260px;
    }

    .address-box {
        background: #f8f8fb;
        border: 1px solid #ececf3;
        border-radius: 10px;
        padding: 10px 12px;
    }

    .tiny-status {
        margin-top: 4px;
        font-size: 12px;
        color: #9a96b3;
        text-transform: capitalize;
    }

    .btn-assign {
        background: #fff;
        border: 1px solid #6c5ce7;
        color: #6c5ce7;
    }

    .btn-assign:hover {
        background: #6c5ce7;
        color: #fff;
    }
</style>
@endpush

@push('js')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.assign-form').forEach(function (form) {

            form.querySelector('.assign-btn').addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Assign this delivery to the order?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Assign',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        });

    });
</script>

@endpush

