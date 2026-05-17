<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a1a;
        width: 100%;
        padding: 12px;
    }
    .center  { text-align: center; }
    .right   { text-align: right; }
    .bold    { font-weight: bold; }
    .divider { border-top: 1px dashed #999; margin: 8px 0; }
    .divider-solid { border-top: 1px solid #333; margin: 8px 0; }

    .header { text-align: center; margin-bottom: 10px; }
    .header .logo-text { font-size: 20px; font-weight: bold; color: #ea580c; }
    .header .restaurant { font-size: 13px; font-weight: bold; }
    .header .sub { font-size: 10px; color: #555; margin-top: 2px; }

    .receipt-meta { font-size: 10px; margin-bottom: 8px; }
    .receipt-meta table { width: 100%; }
    .receipt-meta td { padding: 1px 0; }
    .receipt-meta td:last-child { text-align: right; }

    .items-table { width: 100%; border-collapse: collapse; margin: 6px 0; }
    .items-table th {
        font-size: 10px;
        font-weight: bold;
        border-bottom: 1px solid #333;
        padding: 3px 2px;
        text-align: left;
    }
    .items-table th:last-child,
    .items-table td:last-child { text-align: right; }
    .items-table td { padding: 3px 2px; font-size: 10px; vertical-align: top; }
    .items-table tr:last-child td { border-bottom: 1px solid #333; }

    .totals { width: 100%; margin-top: 6px; }
    .totals td { padding: 2px 0; font-size: 11px; }
    .totals td:last-child { text-align: right; }
    .totals .grand-total td { font-size: 13px; font-weight: bold; padding-top: 4px; }

    .payment-section { margin-top: 8px; font-size: 10px; }
    .footer { text-align: center; margin-top: 12px; font-size: 10px; color: #555; }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 9px;
        font-weight: bold;
        background: #dcfce7;
        color: #166534;
    }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="logo-text">Kusina<span style="color:#1a1a1a;">OMS</span></div>
    <div class="restaurant">{{ $settings['restaurant_name'] }}</div>
    @if($settings['address'])
    <div class="sub">{{ $settings['address'] }}</div>
    @endif
    @if($settings['phone'])
    <div class="sub">Tel: {{ $settings['phone'] }}</div>
    @endif
</div>

<div class="divider-solid"></div>

{{-- Receipt Meta --}}
<div class="receipt-meta">
    <table>
        <tr>
            <td class="bold">Order #</td>
            <td>{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td class="bold">Table</td>
            <td>{{ $order->table?->name ?? 'Takeout' }}</td>
        </tr>
        <tr>
            <td class="bold">Date</td>
            <td>{{ ($order->completed_at ?? $order->created_at)->format('M d, Y g:i A') }}</td>
        </tr>
        @if($order->waiter)
        <tr>
            <td class="bold">Served by</td>
            <td>{{ $order->waiter->name }}</td>
        </tr>
        @endif
        @if($order->cashier)
        <tr>
            <td class="bold">Cashier</td>
            <td>{{ $order->cashier->name }}</td>
        </tr>
        @endif
        @if($order->guests)
        <tr>
            <td class="bold">Guests</td>
            <td>{{ $order->guests }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="divider"></div>

{{-- Order Items --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:45%">Item</th>
            <th style="width:15%;text-align:center;">Qty</th>
            <th style="width:20%;text-align:right;">Price</th>
            <th style="width:20%;text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>{{ $item->menuItem?->name ?? 'Item' }}</td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align:right;">{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @if($item->notes)
        <tr>
            <td colspan="4" style="color:#666;font-size:9px;padding-left:8px;">
                Note: {{ $item->notes }}
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>

{{-- Totals --}}
<table class="totals">
    <tr>
        <td>Subtotal</td>
        <td>{{ $settings['currency'] }} {{ number_format($order->subtotal, 2) }}</td>
    </tr>
    @if($order->discount_amount > 0)
    <tr>
        <td>Discount</td>
        <td style="color:#dc2626;">- {{ $settings['currency'] }} {{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td>Tax ({{ $settings['tax_rate'] }}%)</td>
        <td>{{ $settings['currency'] }} {{ number_format($order->tax_amount, 2) }}</td>
    </tr>
    <tr class="grand-total">
        <td>TOTAL</td>
        <td>{{ $settings['currency'] }} {{ number_format($order->total_amount, 2) }}</td>
    </tr>
</table>

{{-- Payment Info --}}
@if($order->payment)
<div class="divider"></div>
<div class="payment-section">
    <table style="width:100%">
        <tr>
            <td class="bold">Payment Method</td>
            <td style="text-align:right;text-transform:capitalize;">{{ $order->payment->method ?? '—' }}</td>
        </tr>
        @if($order->payment->amount_tendered)
        <tr>
            <td>Amount Tendered</td>
            <td style="text-align:right;">
                {{ $settings['currency'] }} {{ number_format($order->payment->amount_tendered, 2) }}
            </td>
        </tr>
        <tr>
            <td>Change</td>
            <td style="text-align:right;">
                {{ $settings['currency'] }}
                {{ number_format($order->payment->amount_tendered - $order->total_amount, 2) }}
            </td>
        </tr>
        @endif
    </table>
</div>
@endif

@if($order->notes)
<div class="divider"></div>
<div style="font-size:10px;color:#555;">
    <span class="bold">Notes:</span> {{ $order->notes }}
</div>
@endif

<div class="divider-solid"></div>

<div class="footer">
    <p>Thank you for dining with us!</p>
    <p style="margin-top:4px;color:#999;font-size:9px;">
        Generated {{ now()->format('M d, Y g:i A') }}
    </p>
</div>

</body>
</html>