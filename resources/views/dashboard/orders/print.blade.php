@php
    use App\Models\Order;

    $isRtl = app()->getLocale() === 'ar';
    $dashboardSetting = $setting ?? null;
    $storeName = trim((string) ($dashboardSetting?->site_name ?: config('app.name')));
    $paymentMethodKey = $order->payment_method
        ? 'dashboard.payment-method-' . str_replace('_', '-', $order->payment_method)
        : null;
    $paymentStatusKey = $order->payment_status
        ? 'dashboard.payment-status-' . str_replace('_', '-', $order->payment_status)
        : null;
    $orderStatusKey = $order->status
        ? 'dashboard.order-status-' . str_replace('_', '-', $order->status)
        : null;
    $paymentMethodLabel = $paymentMethodKey ? __($paymentMethodKey) : '-';
    $paymentStatusLabel = $paymentStatusKey ? __($paymentStatusKey) : '-';
    $orderStatusLabel = $orderStatusKey ? __($orderStatusKey) : '-';
    $deliveryMethod = data_get($order->meta, 'delivery_method_label')
        ?? data_get($order->meta, 'delivery_method')
        ?? ($address !== [] ? __('dashboard.shipping') : null);
    $customerName = $order->user?->name ?? data_get($address, 'contact_name') ?? '-';
    $customerPhone = $order->user?->phone ?? data_get($address, 'phone') ?? '-';
    $addressParts = collect([
        data_get($address, 'country_name'),
        data_get($address, 'governorate_name'),
        data_get($address, 'region_name'),
        data_get($address, 'city'),
        data_get($address, 'area'),
        data_get($address, 'street'),
        data_get($address, 'building_number') ? __('dashboard.building-number') . ': ' . data_get($address, 'building_number') : null,
        data_get($address, 'floor') ? __('dashboard.floor') . ': ' . data_get($address, 'floor') : null,
        data_get($address, 'apartment_number') ? __('dashboard.apartment-number') . ': ' . data_get($address, 'apartment_number') : null,
        data_get($address, 'landmark') ? __('dashboard.landmark') . ': ' . data_get($address, 'landmark') : null,
    ])->filter(fn ($value) => filled($value));
    $addressText = data_get($address, 'full_address') ?: ($addressParts->isNotEmpty() ? $addressParts->implode(', ') : null);
    $paidAmount = 0.0;

    if ($order->walletTransaction) {
        $paidAmount = min((float) $order->total, abs((float) $order->walletTransaction->amount));
    } elseif (in_array($order->payment_status, [Order::PAYMENT_STATUS_PAID, Order::PAYMENT_STATUS_REFUNDED], true)) {
        $paidAmount = (float) $order->total;
    }

    $remainingAmount = max((float) $order->total - $paidAmount, 0);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('dashboard.invoice') }} - {{ $order->order_number }}</title>
    <style>
        :root {
            --receipt-width: 380px;
            --receipt-bg: #ffffff;
            --receipt-page: #f4f6f8;
            --receipt-text: #111827;
            --receipt-muted: #6b7280;
            --receipt-line: #d7dde5;
            --receipt-accent: #155eef;
            --receipt-strong: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--receipt-page);
            color: var(--receipt-text);
            font-family: "Segoe UI", Tahoma, "Arial Unicode MS", sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .screen-only {
            display: flex;
        }

        .screen-toolbar {
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem;
        }

        .toolbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1rem;
            border-radius: 999px;
            border: 1px solid transparent;
            background: var(--receipt-accent);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }

        .toolbar-button.secondary {
            background: transparent;
            border-color: var(--receipt-line);
            color: var(--receipt-strong);
        }

        .invoice-wrapper {
            padding: 0 1rem 2rem;
        }

        .invoice-sheet {
            width: min(100%, var(--receipt-width));
            margin: 0 auto;
            padding: 1.35rem 1rem 1.15rem;
            background: var(--receipt-bg);
            border: 1px solid var(--receipt-line);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .invoice-header {
            text-align: center;
        }

        .store-name {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--receipt-strong);
            line-height: 1.3;
        }

        .invoice-title {
            margin-top: 0.35rem;
            font-size: 0.72rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--receipt-muted);
        }

        .divider {
            margin: 0.95rem 0;
            border-top: 1px dashed var(--receipt-line);
        }

        .details-grid,
        .info-list,
        .totals-list {
            display: grid;
            gap: 0.55rem;
        }

        .detail-row,
        .info-row,
        .total-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.83rem;
            line-height: 1.45;
        }

        .detail-row span:first-child,
        .info-row span:first-child,
        .total-row span:first-child {
            color: var(--receipt-muted);
        }

        .detail-value,
        .info-value,
        .total-value {
            max-width: 62%;
            color: var(--receipt-strong);
            text-align: right;
            font-weight: 600;
            word-break: break-word;
        }

        body[dir="rtl"] .detail-value,
        body[dir="rtl"] .info-value,
        body[dir="rtl"] .total-value {
            text-align: left;
        }

        .section {
            margin-top: 1rem;
        }

        .section-title {
            margin: 0 0 0.7rem;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--receipt-muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.55rem;
            border-radius: 999px;
            background: rgba(21, 94, 239, 0.08);
            color: var(--receipt-accent);
            font-size: 0.76rem;
            font-weight: 700;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            padding: 0 0 0.55rem;
            border-bottom: 1px dashed var(--receipt-line);
            color: var(--receipt-muted);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .items-table tbody td {
            padding: 0.7rem 0;
            border-bottom: 1px dashed var(--receipt-line);
            font-size: 0.83rem;
            vertical-align: top;
        }

        .items-table .text-end {
            text-align: right;
        }

        body[dir="rtl"] .items-table .text-end {
            text-align: left;
        }

        .item-name {
            display: block;
            font-weight: 700;
            color: var(--receipt-strong);
            margin-bottom: 0.2rem;
        }

        .item-meta {
            display: block;
            font-size: 0.72rem;
            color: var(--receipt-muted);
            line-height: 1.45;
        }

        .totals-list {
            margin-top: 0.2rem;
        }

        .total-row.grand-total {
            padding-top: 0.7rem;
            border-top: 1px solid var(--receipt-line);
            font-size: 0.95rem;
            font-weight: 800;
        }

        .footer {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--receipt-muted);
            line-height: 1.7;
        }

        .footer strong {
            color: var(--receipt-strong);
        }

        @page {
            size: auto;
            margin: 8mm;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .screen-only {
                display: none !important;
            }

            .invoice-wrapper {
                padding: 0;
            }

            .invoice-sheet {
                width: 80mm;
                max-width: 100%;
                margin: 0 auto;
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 480px) {
            .screen-toolbar {
                padding-inline: 0.75rem;
            }

            .toolbar-button {
                flex: 1 1 auto;
            }

            .invoice-wrapper {
                padding: 0 0.5rem 1rem;
            }

            .invoice-sheet {
                width: 100%;
                border-radius: 16px;
                padding: 1.1rem 0.85rem 1rem;
            }
        }
    </style>
</head>

<body dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="screen-toolbar screen-only">
        <button type="button" class="toolbar-button" onclick="window.print()">{{ __('dashboard.print-invoice') }}</button>
        <a href="{{ route('dashboard.orders.show', $order) }}" class="toolbar-button secondary">{{ __('dashboard.back') }}</a>
    </div>

    <div class="invoice-wrapper">
        <main class="invoice-sheet">
            <header class="invoice-header">
                <div class="store-name">{{ $storeName !== '' ? $storeName : config('app.name') }}</div>
                <div class="invoice-title">{{ __('dashboard.invoice') }}</div>
                <div class="divider"></div>

                <div class="details-grid">
                    <div class="detail-row">
                        <span>{{ __('dashboard.order-number') }}</span>
                        <span class="detail-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span>ID</span>
                        <span class="detail-value">#{{ $order->id }}</span>
                    </div>
                    <div class="detail-row">
                        <span>{{ __('dashboard.placed-at') }}</span>
                        <span class="detail-value">{{ $order->placed_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span>{{ __('dashboard.printed-at') }}</span>
                        <span class="detail-value">{{ $printedAt?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                </div>
            </header>

            <section class="section">
                <h2 class="section-title">{{ __('dashboard.customer-info') }}</h2>
                <div class="info-list">
                    <div class="info-row">
                        <span>{{ __('dashboard.customer') }}</span>
                        <span class="info-value">{{ $customerName }}</span>
                    </div>
                    <div class="info-row">
                        <span>{{ __('dashboard.phone') }}</span>
                        <span class="info-value">{{ $customerPhone }}</span>
                    </div>
                    <div class="info-row">
                        <span>{{ __('dashboard.address-details') }}</span>
                        <span class="info-value">{{ $addressText ?: '-' }}</span>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">{{ __('dashboard.order-details') }}</h2>
                <div class="info-list">
                    <div class="info-row">
                        <span>{{ __('dashboard.order-status') }}</span>
                        <span class="info-value"><span class="status-pill">{{ $orderStatusLabel }}</span></span>
                    </div>
                    <div class="info-row">
                        <span>{{ __('dashboard.payment-method') }}</span>
                        <span class="info-value">{{ $paymentMethodLabel }}</span>
                    </div>
                    <div class="info-row">
                        <span>{{ __('dashboard.payment-status') }}</span>
                        <span class="info-value">{{ $paymentStatusLabel }}</span>
                    </div>
                    @if ($deliveryMethod)
                        <div class="info-row">
                            <span>{{ __('dashboard.delivery-method') }}</span>
                            <span class="info-value">{{ $deliveryMethod }}</span>
                        </div>
                    @endif
                    @if ($order->notes)
                        <div class="info-row">
                            <span>{{ __('dashboard.notes') }}</span>
                            <span class="info-value">{{ $order->notes }}</span>
                        </div>
                    @endif
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">{{ __('dashboard.order-items') }}</h2>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.product') }}</th>
                            <th class="text-end">{{ __('dashboard.qty') }}</th>
                            <th class="text-end">{{ __('dashboard.unit-price') }}</th>
                            <th class="text-end">{{ __('dashboard.line-total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            @php
                                $itemMeta = collect([
                                    $item->product?->sku ? __('dashboard.sku') . ': ' . $item->product->sku : null,
                                    $item->product_id ? 'ID: #' . $item->product_id : null,
                                ])->filter()->implode(' | ');
                            @endphp
                            <tr>
                                <td>
                                    <span class="item-name">{{ $item->product_name }}</span>
                                    @if ($itemMeta !== '')
                                        <span class="item-meta">{{ $itemMeta }}</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-end">{{ __('dashboard.no-data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="section">
                <h2 class="section-title">{{ __('dashboard.pricing-summary') }}</h2>
                <div class="totals-list">
                    <div class="total-row">
                        <span>{{ __('dashboard.subtotal') }}</span>
                        <span class="total-value">{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>{{ __('dashboard.delivery-fee') }}</span>
                        <span class="total-value">{{ number_format((float) $order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>{{ __('dashboard.discount') }}</span>
                        <span class="total-value">{{ number_format((float) $order->discount_amount, 2) }}</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>{{ __('dashboard.total') }}</span>
                        <span class="total-value">{{ number_format((float) $order->total, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>{{ __('dashboard.paid') }}</span>
                        <span class="total-value">{{ number_format($paidAmount, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>{{ __('dashboard.remaining') }}</span>
                        <span class="total-value">{{ number_format($remainingAmount, 2) }}</span>
                    </div>
                </div>
            </section>

            <footer class="footer">
                <div><strong>{{ __('dashboard.thank-you') }}</strong></div>
                @if ($dashboardSetting?->site_phone)
                    <div>{{ __('dashboard.support-phone') }}: {{ $dashboardSetting->site_phone }}</div>
                @endif
                @if ($dashboardSetting?->site_email)
                    <div>{{ $dashboardSetting->site_email }}</div>
                @endif
            </footer>
        </main>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (!window.location.hash.includes('no-print')) {
                window.print();
            }
        });
    </script>
</body>

</html>
