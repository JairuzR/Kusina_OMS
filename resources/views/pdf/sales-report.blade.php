<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; padding: 24px; }

    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 3px solid #ea580c; padding-bottom: 12px; }
    .brand .logo { font-size: 22px; font-weight: bold; }
    .brand .logo span { color: #ea580c; }
    .brand .sub { font-size: 10px; color: #666; margin-top: 2px; }
    .report-title { text-align: right; }
    .report-title h1 { font-size: 18px; font-weight: bold; color: #1a1a1a; }
    .report-title .period { font-size: 10px; color: #666; margin-top: 3px; }
    .report-title .generated { font-size: 9px; color: #999; margin-top: 2px; }

    .summary-grid { display: table; width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 8px; }
    .summary-card { display: table-cell; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; text-align: center; }
    .summary-card .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-card .value { font-size: 16px; font-weight: bold; color: #111827; margin-top: 4px; }
    .summary-card .value.green { color: #16a34a; }

    .section-title { font-size: 12px; font-weight: bold; color: #374151; margin: 16px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }

    table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table.data thead tr { background: #1f2937; color: #fff; }
    table.data thead th { padding: 7px 10px; text-align: left; font-size: 10px; font-weight: bold; }
    table.data thead th.right { text-align: right; }
    table.data tbody tr:nth-child(even) { background: #f9fafb; }
    table.data tbody td { padding: 6px 10px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    table.data tbody td.right { text-align: right; }
    table.data tfoot tr { background: #f3f4f6; font-weight: bold; }
    table.data tfoot td { padding: 7px 10px; font-size: 11px; border-top: 2px solid #d1d5db; }
    table.data tfoot td.right { text-align: right; }

    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-red    { background: #fee2e2; color: #991b1b; }

    .page-footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }

    .top-items { display: table; width: 100%; border-collapse: collapse; }
    .top-item-row { display: table-row; }
    .top-item-cell { display: table-cell; padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
</style>
</head>
<body>

{{-- Page Header --}}
<div class="page-header">
    <div class="brand">
        <div class="logo"><span>Kusina</span>OMS</div>
        <div class="sub">{{ $settings['restaurant_name'] }}</div>
        @if($settings['address'])<div class="sub">{{ $settings['address'] }}</div>@endif
    </div>
    <div class="report-title">
        <h1>Sales Report</h1>
        <div class="period">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
        <div class="generated">Generated: {{ now()->format('F d, Y g:i A') }}</div>
    </div>
</div>

{{-- Summary Cards --}}
<table class="summary-grid">
    <tr>
        <td class="summary-card">
            <div class="label">Total Orders</div>
            <div class="value">{{ number_format($summary['total_orders']) }}</div>
        </td>
        <td class="summary-card">
            <div class="label">Total Revenue</div>
            <div class="value green">{{ $settings['currency'] }} {{ number_format($summary['total_revenue'], 2) }}</div>
        </td>
        <td class="summary-card">
            <div class="label">Total Tax</div>
            <div class="value">{{ $settings['currency'] }} {{ number_format($summary['total_tax'], 2) }}</div>
        </td>
        <td class="summary-card">
            <div class="label">Total Discounts</div>
            <div class="value">{{ $settings['currency'] }} {{ number_format($summary['total_discount'], 2) }}</div>
        </td>
        <td class="summary-card">
            <div class="label">Avg Order Value</div>
            <div class="value">{{ $settings['currency'] }} {{ number_format($summary['average_order'], 2) }}</div>
        </td>
    </tr>
</table>

{{-- Top Selling Items --}}
@if(count($topItems) > 0)
<div class="section-title">Top Selling Items</div>
<table class="data">
    <thead>
        <tr>
            <th>#</th>
            <th>Item Name</th>
            <th class="right">Qty Sold</th>
            <th class="right">Revenue</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topItems as $name => $data)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $name }}</td>
            <td class="right">{{ number_format($data['quantity']) }}</td>
            <td class="right">{{ $settings['currency'] }} {{ number_format($data['revenue'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Orders Table --}}
<div class="section-title">Order Details</div>
<table class="data">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Table</th>
            <th>Date</th>
            <th class="right">Subtotal</th>
            <th class="right">Discount</th>
            <th class="right">Tax</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->table?->name ?? 'Takeout' }}</td>
            <td>{{ $order->completed_at?->format('M d, Y g:i A') ?? '—' }}</td>
            <td class="right">{{ number_format($order->subtotal, 2) }}</td>
            <td class="right">
                @if($order->discount_amount > 0)
                    {{ number_format($order->discount_amount, 2) }}
                @else —
                @endif
            </td>
            <td class="right">{{ number_format($order->tax_amount, 2) }}</td>
            <td class="right">{{ $settings['currency'] }} {{ number_format($order->total_amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:16px;">No orders found for this period.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTALS</td>
            <td class="right">{{ number_format($summary['total_discount'], 2) }}</td>
            <td class="right">{{ number_format($summary['total_tax'], 2) }}</td>
            <td class="right">{{ $settings['currency'] }} {{ number_format($summary['total_revenue'], 2) }}</td>
        </tr>
    </tfoot>
</table>

{{-- Footer --}}
<div class="page-footer">
    <span>KusinaOMS — {{ $settings['restaurant_name'] }}</span>
    <span>Page 1 | Confidential</span>
    <span>{{ now()->format('Y-m-d H:i:s') }}</span>
</div>

</body>
</html>