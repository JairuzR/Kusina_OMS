<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period    = $request->get('period', 'this_month');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');

        [$from, $to] = $this->resolveDateRange($period, $dateFrom, $dateTo);

        // Sales summary
        $completedOrders = Order::whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['completed', 'billed']);

        $totalRevenue  = (clone $completedOrders)->sum('total_amount');
        $totalOrders   = (clone $completedOrders)->count();
        $averageOrder  = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalDiscount = (clone $completedOrders)->sum('discount_amount');

        // Daily revenue for chart (last 30 days or selected range)
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['completed', 'billed'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling items
        $topItems = OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('menuItem:id,name,price')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$from, $to])->whereIn('status', ['completed', 'billed']))
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Orders by type
        $ordersByType = Order::selectRaw('type, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['completed', 'billed'])
            ->groupBy('type')
            ->get();

        // Orders by status (all statuses for the period)
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->get();

        // Low stock items
        $lowStockItems = InventoryItem::where('is_active', true)
            ->whereRaw('quantity <= min_quantity')
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        // Revenue by payment method
        $revenueByPayment = DB::table('payments')
            ->select('payment_method', DB::raw('SUM(amount_paid) as total'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact(
            'period', 'from', 'to',
            'totalRevenue', 'totalOrders', 'averageOrder', 'totalDiscount',
            'dailyRevenue', 'topItems', 'ordersByType', 'ordersByStatus',
            'lowStockItems', 'revenueByPayment'
        ));
    }

    public function exportCsv(Request $request)
    {
        $type   = $request->get('type', 'sales');
        $period = $request->get('period', 'this_month');
        [$from, $to] = $this->resolveDateRange($period, $request->get('date_from'), $request->get('date_to'));

        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($type, $from, $to) {
            $handle = fopen('php://output', 'w');

            if ($type === 'sales') {
                fputcsv($handle, ['Order #', 'Date', 'Type', 'Status', 'Items', 'Subtotal', 'Discount', 'Tax', 'Total', 'Payment Method']);
                Order::with(['items', 'payment'])
                    ->whereBetween('created_at', [$from, $to])
                    ->orderBy('created_at')
                    ->chunk(200, function ($orders) use ($handle) {
                        foreach ($orders as $order) {
                            fputcsv($handle, [
                                $order->order_number,
                                $order->created_at->format('Y-m-d H:i'),
                                $order->type,
                                $order->status,
                                $order->items->count(),
                                $order->subtotal,
                                $order->discount_amount,
                                $order->tax_amount,
                                $order->total_amount,
                                $order->payment?->payment_method ?? '—',
                            ]);
                        }
                    });
            } elseif ($type === 'inventory') {
                fputcsv($handle, ['SKU', 'Name', 'Category', 'Unit', 'Quantity', 'Min Quantity', 'Status', 'Cost/Unit', 'Supplier']);
                InventoryItem::with('supplier')->orderBy('name')->chunk(200, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $item->sku,
                            $item->name,
                            $item->category,
                            $item->unit,
                            $item->quantity,
                            $item->min_quantity,
                            $item->isLowStock() ? 'Low Stock' : 'OK',
                            $item->cost_per_unit,
                            $item->supplier?->name ?? '—',
                        ]);
                    }
                });
            } elseif ($type === 'top_items') {
                fputcsv($handle, ['Item', 'Qty Sold', 'Revenue']);
                OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                    ->with('menuItem:id,name')
                    ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$from, $to])->whereIn('status', ['completed', 'billed']))
                    ->groupBy('menu_item_id')
                    ->orderByDesc('total_qty')
                    ->get()
                    ->each(fn($row) => fputcsv($handle, [
                        $row->menuItem?->name ?? '—',
                        $row->total_qty,
                        number_format($row->total_revenue, 2),
                    ]));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        return match ($period) {
            'today'        => [now()->startOfDay(),   now()->endOfDay()],
            'yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week'    => [now()->startOfWeek(),  now()->endOfWeek()],
            'last_week'    => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'this_month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year'    => [now()->startOfYear(),  now()->endOfYear()],
            'custom'       => [
                \Carbon\Carbon::parse($dateFrom)->startOfDay(),
                \Carbon\Carbon::parse($dateTo)->endOfDay(),
            ],
            default        => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}