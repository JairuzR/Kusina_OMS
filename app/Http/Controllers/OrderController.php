<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table', 'waiter'])
            ->latest()
            ->paginate(15);

        $stats = [
            'pending'   => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready'     => Order::where('status', 'ready')->count(),
            'billed'    => Order::where('status', 'billed')->count(),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $tables     = Table::where('status', 'available')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();
        $categories = MenuCategory::with(['items' => function ($q) {
                            $q->where('is_available', true)->orderBy('name');
                        }])
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        return view('orders.create', compact('tables', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'type'     => 'required|in:dine_in,takeout,delivery',
            'guests'   => 'required|integer|min:1',
            'notes'    => 'nullable|string',
            'items'    => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'table_id'     => $validated['table_id'] ?? null,
                'waiter_id'    => auth()->id(),
                'type'         => $validated['type'],
                'guests'       => $validated['guests'],
                'notes'        => $validated['notes'] ?? null,
                'status'       => 'pending',
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $itemSubtotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id'              => $order->id,
                    'menu_item_id'          => $menuItem->id,
                    'quantity'              => $item['quantity'],
                    'unit_price'            => $menuItem->price,
                    'subtotal'              => $itemSubtotal,
                    'special_instructions'  => $item['special_instructions'] ?? null,
                    'status'                => 'pending',
                ]);
            }

            $taxRate   = (float) Setting::get('tax_rate', 12) / 100;
            $taxAmount = $subtotal * $taxRate;
            $total     = $subtotal + $taxAmount;

            $order->update([
                'subtotal'   => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
            ]);

            // Mark table as occupied
            if ($order->table_id) {
                Table::where('id', $order->table_id)
                    ->update(['status' => 'occupied']);
            }

            AuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'created',
                'module'      => 'orders',
                'model_type'  => Order::class,
                'model_id'    => $order->id,
                'description' => 'Created order ' . $order->order_number,
                'ip_address'  => request()->ip(),
            ]);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['table', 'waiter', 'cashier', 'items.menuItem', 'payment']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,billed,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'completed' && $order->table_id) {
            Table::where('id', $order->table_id)
                ->update(['status' => 'needs_cleaning']);
        }

        if ($request->status === 'cancelled' && $order->table_id) {
            Table::where('id', $order->table_id)
                ->update(['status' => 'available']);
        }

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'updated',
            'module'      => 'orders',
            'model_type'  => Order::class,
            'model_id'    => $order->id,
            'description' => 'Updated order ' . $order->order_number . ' status to ' . $request->status,
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', 'Order status updated.');
    }

    public function generateBill(Request $request, Order $order)
    {
        $request->validate([
            'discount_type'   => 'nullable|in:senior,pwd,promo',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $discountAmount = (float) ($request->discount_amount ?? 0);
        $total = $order->subtotal + $order->tax_amount - $discountAmount;

        $order->update([
            'discount_type'   => $request->discount_type,
            'discount_amount' => $discountAmount,
            'total_amount'    => max(0, $total),
            'status'          => 'billed',
        ]);

        return back()->with('success', 'Bill generated.');
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,gcash,credit_card,debit_card',
            'amount_paid'    => 'required|numeric|min:0',
            'reference_number' => 'nullable|string',
        ]);

        $change = (float) $request->amount_paid - (float) $order->total_amount;

        DB::transaction(function () use ($request, $order, $change) {
            Payment::create([
                'order_id'         => $order->id,
                'cashier_id'       => auth()->id(),
                'payment_method'   => $request->payment_method,
                'amount_paid'      => $request->amount_paid,
                'change_amount'    => max(0, $change),
                'reference_number' => $request->reference_number,
                'status'           => 'completed',
            ]);

            $order->update([
                'status'       => 'completed',
                'cashier_id'   => auth()->id(),
                'completed_at' => now(),
            ]);

            if ($order->table_id) {
                Table::where('id', $order->table_id)
                    ->update(['status' => 'needs_cleaning']);
            }
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment processed successfully.');
    }

    public function destroy(Order $order)
    {
        $order->update(['status' => 'cancelled']);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order cancelled and removed.');
    }
}