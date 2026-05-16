<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table', 'items.menuItem'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at')
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    public function updateItemStatus(Request $request, OrderItem $item)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,served',
        ]);

        $item->update([
            'status'      => $request->status,
            'prepared_at' => $request->status === 'ready' ? now() : $item->prepared_at,
        ]);

        // Auto-update order status based on items
        $order = $item->order;
        $allItems = $order->items;

        if ($allItems->every(fn($i) => $i->status === 'ready')) {
            $order->update(['status' => 'ready']);
        } elseif ($allItems->contains(fn($i) => $i->status === 'preparing')) {
            $order->update(['status' => 'preparing']);
        }

        return back()->with('success', 'Item status updated.');
    }
}