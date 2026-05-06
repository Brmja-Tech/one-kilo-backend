<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title }}</title>

        <style>
            :root {
                --bg: #f6f7fb;
                --card: #ffffff;
                --text: #111827;
                --muted: #6b7280;
                --border: #e5e7eb;
                --success: #16a34a;
                --danger: #dc2626;
                --warning: #d97706;
                --btn: #111827;
                --btnText: #ffffff;
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                padding: 24px;
                background: var(--bg);
                color: var(--text);
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            }
            .wrap {
                min-height: calc(100vh - 48px);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .card {
                width: 100%;
                max-width: 420px;
                background: var(--card);
                border: 1px solid var(--border);
                border-radius: 16px;
                padding: 22px 20px;
                text-align: center;
                box-shadow: 0 14px 34px rgba(0,0,0,.08);
            }
            .icon {
                width: 56px;
                height: 56px;
                margin: 6px auto 14px;
                border-radius: 999px;
                display: grid;
                place-items: center;
                color: #fff;
            }
            .icon.success { background: var(--success); }
            .icon.failed { background: var(--danger); }
            .icon.pending { background: var(--warning); }
            h1 {
                margin: 0 0 8px;
                font-size: 20px;
                line-height: 1.25;
            }
            p {
                margin: 0 0 16px;
                color: var(--muted);
                font-size: 14px;
                line-height: 1.6;
            }
            .meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 12px 14px;
                border: 1px solid var(--border);
                border-radius: 12px;
                margin: 0 0 16px;
                text-align: left;
            }
            .meta .label {
                color: var(--muted);
                font-size: 12px;
            }
            .meta .value {
                font-weight: 600;
                font-size: 13px;
                word-break: break-word;
                text-align: right;
            }
            .btn {
                display: inline-block;
                width: 100%;
                padding: 12px 14px;
                border-radius: 12px;
                background: var(--btn);
                color: var(--btnText);
                text-decoration: none;
                font-weight: 600;
                font-size: 14px;
            }
            .hint {
                margin-top: 12px;
                font-size: 12px;
                color: var(--muted);
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <div class="card">
                <div class="icon {{ $state }}">
                    @if($state === 'success')
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @elseif($state === 'failed')
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M18 6L6 18M6 6l12 12" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @else
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 8v5m0 4h.01" stroke="white" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                </div>

                <h1>{{ $title }}</h1>
                <p>{{ $message }}</p>

                @if(!empty($orderNumber))
                    <div class="meta">
                        <div class="label">{{ __('front.order-number') }}</div>
                        <div class="value">{{ $orderNumber }}</div>
                    </div>
                @endif

                <a class="btn" href="https://admin.hyberonekilo.com/api/payment-callback?status={{ $state }}&order_number={{ $orderNumber }}">
                    {{ $buttonText }}
                </a>

                <div class="hint">{{ __('front.you-can-close-this-page') }}</div>
            </div>
        </div>
    </body>
</html>

