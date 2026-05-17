<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; padding: 20px; }

    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 3px solid #ea580c; padding-bottom: 10px; }
    .brand .logo { font-size: 20px; font-weight: bold; }
    .brand .logo span { color: #ea580c; }
    .brand .sub { font-size: 10px; color: #666; margin-top: 2px; }
    .report-title { text-align: right; }
    .report-title h1 { font-size: 16px; font-weight: bold; }
    .report-title .generated { font-size: 9px; color: #999; margin-top: 3px; }

    .summary-row { display: table; width: 100%; margin-bottom: 14px; }
    .summary-cell { display: table-cell; background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 12px; text-align: center; }
    .summary-cell .lbl { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .summary-cell .val { font-size: 14px; font-weight: bold; margin-top: 3px; }

    table.data { width: 100%; border-collapse: collapse; }
    table.data thead tr { background: #1f2937; color: #fff; }
    table.data thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; }
    table.data thead th.right { text-align: right; }
    table.data tbody tr:nth-child(even) { background: #f9fafb; }
    table.data tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    table.data tbody td.right { text-align: right; }

    .status-ok       { color: #16a34a; font-weight: bold; }
    .status-low      { color: #d97706; font-weight: bold; }
    .status-out      { color: #dc2626; font-weight: bold; }

    .cat-header td { background: #f3f4f6; font-weight: bold; font-size: 10px; padding: 5px 8px; color: #374151; }

    .page-footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>

{{-- Header --}}
<div class="page-header">
    <div class="brand">
        <div class="logo"><span>Kusina</span>OMS</div>
        <div class="sub">{{ $settings['restaurant_name'] }}</div>
        @if($settings['address'])<div class="sub">{{ $settings['address'] }}</div>@endif
    </div>
    <div class="report-title">
        <h1>Inventory Report</h1>
        <div class="generated">Generated: {{ now()->format('F d, Y g:i A') }}</div>
    </div>
</div>

{{-- Summary --}}
<table class="summary-row">
    <tr>
        <td class="summary-cell">
            <div class="lbl">Total Items</div>
            <div class="val">{{ $summary['total_items'] }}</div>
        </td>
        <td class="summary-cell">
            <div class="lbl">Low Stock</div>
            <div class="val" style="color:#d97706;">{{ $summary['low_stock'] }}</div>
        </td>
        <td class="summary-cell">
            <div class="lbl">Out of Stock</div>
            <div class="val" style="color:#dc2626;">{{ $summary['out_of_stock'] }}</div>
        </td>
        <td class="summary-cell">
            <div class="lbl">Total Value</div>
            <div class="val" style="color:#16a34a;">
                {{ $settings['currency'] }} {{ number_format($summary['total_value'], 2) }}
            </div>
        </td>
    </tr>
</table>

{{-- Items by Category --}}
@php $grouped = $items->groupBy('category'); @endphp

<table class="data">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Unit</th>
            <th class="right">Current Stock</th>
            <th class="right">Min. Level</th>
            <th class="right">Cost/Unit</th>
            <th class="right">Total Value</th>
            <th>Supplier</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grouped as $category => $categoryItems)
        <tr class="cat-header">
            <td colspan="8">{{ $category ?: 'Uncategorized' }} ({{ $categoryItems->count() }} items)</td>
        </tr>
        @foreach($categoryItems as $item)
        @php
            $status = $item->quantity == 0
                ? 'out'
                : ($item->quantity <= $item->min_quantity ? 'low' : 'ok');
        @endphp
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->unit }}</td>
            <td class="right">{{ number_format($item->quantity, 2) }}</td>
            <td class="right">{{ number_format($item->min_quantity, 2) }}</td>
            <td class="right">{{ number_format($item->cost_per_unit, 2) }}</td>
            <td class="right">{{ $settings['currency'] }} {{ number_format($item->quantity * $item->cost_per_unit, 2) }}</td>
            <td>{{ $item->supplier?->name ?? '—' }}</td>
            <td class="status-{{ $status }}">
                {{ $status === 'out' ? 'Out of Stock' : ($status === 'low' ? 'Low Stock' : 'OK') }}
            </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

<div class="page-footer">
    <span>KusinaOMS — {{ $settings['restaurant_name'] }}</span>
    <span>Confidential — Internal Use Only</span>
    <span>{{ now()->format('Y-m-d H:i:s') }}</span>
</div>

</body>
</html>