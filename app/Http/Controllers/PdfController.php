<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    // ── Order Receipt ──────────────────────────────────────────────────────────

    public function orderReceipt(Order $order)
    {
        $order->load(['items.menuItem', 'table', 'waiter', 'cashier', 'payment']);

        $settings = [
            'restaurant_name' => Setting::get('restaurant_name', 'KusinaOMS'),
            'address'         => Setting::get('address', ''),
            'phone'           => Setting::get('phone', ''),
            'tax_rate'        => Setting::get('tax_rate', '12'),
            'currency'        => Setting::get('currency', 'PHP'),
        ];

        $pdf = Pdf::loadView('pdf.order-receipt', compact('order', 'settings'))
            ->setPaper([0, 0, 226.77, 600], 'portrait') // 80mm thermal receipt width
            ->setOptions([
                'defaultFont'     => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->download('receipt_order_' . $order->order_number . '.pdf');
    }

    // ── Sales Report PDF ───────────────────────────────────────────────────────

    public function salesReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $orders = Order::with(['items.menuItem', 'table', 'payment'])
            ->where('status', 'completed')
            ->whereDate('completed_at', '>=', $dateFrom)
            ->whereDate('completed_at', '<=', $dateTo)
            ->latest('completed_at')
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total_amount'),
            'total_tax'      => $orders->sum('tax_amount'),
            'total_discount' => $orders->sum('discount_amount'),
            'average_order'  => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
        ];

        // Top items
        $itemSales = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $name = $item->menuItem?->name ?? 'Unknown';
                if (!isset($itemSales[$name])) {
                    $itemSales[$name] = ['quantity' => 0, 'revenue' => 0];
                }
                $itemSales[$name]['quantity'] += $item->quantity;
                $itemSales[$name]['revenue']  += $item->subtotal;
            }
        }
        arsort($itemSales);
        $topItems = array_slice($itemSales, 0, 10, true);

        $settings = [
            'restaurant_name' => Setting::get('restaurant_name', 'KusinaOMS'),
            'address'         => Setting::get('address', ''),
            'phone'           => Setting::get('phone', ''),
            'currency'        => Setting::get('currency', 'PHP'),
        ];

        $pdf = Pdf::loadView('pdf.sales-report', compact(
            'orders', 'summary', 'topItems', 'settings', 'dateFrom', 'dateTo'
        ))->setPaper('a4', 'portrait')
          ->setOptions([
              'defaultFont'          => 'sans-serif',
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled'      => false,
          ]);

        $filename = 'sales_report_' . $dateFrom . '_to_' . $dateTo . '.pdf';

        return $pdf->download($filename);
    }

    // ── Inventory Report PDF ───────────────────────────────────────────────────

    public function inventoryReport()
    {
        $items = InventoryItem::with('supplier')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $summary = [
            'total_items'    => $items->count(),
            'low_stock'      => $items->where('quantity', '<=', 'min_quantity')->count(),
            'total_value'    => $items->sum(fn($i) => $i->quantity * $i->cost_per_unit),
            'out_of_stock'   => $items->where('quantity', 0)->count(),
        ];

        // Recalculate low stock properly
        $summary['low_stock'] = $items->filter(
            fn($i) => $i->quantity <= $i->min_quantity && $i->quantity > 0
        )->count();

        $settings = [
            'restaurant_name' => Setting::get('restaurant_name', 'KusinaOMS'),
            'address'         => Setting::get('address', ''),
            'currency'        => Setting::get('currency', 'PHP'),
        ];

        $pdf = Pdf::loadView('pdf.inventory-report', compact('items', 'summary', 'settings'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        $filename = 'inventory_report_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}