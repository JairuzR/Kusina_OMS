<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::with('supplier')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(15);

        $stats = [
            'total'     => InventoryItem::where('is_active', true)->count(),
            'low_stock' => InventoryItem::where('is_active', true)
                            ->whereColumn('quantity', '<=', 'min_quantity')
                            ->count(),
            'out_of_stock' => InventoryItem::where('is_active', true)
                                ->where('quantity', 0)
                                ->count(),
            'suppliers' => Supplier::where('status', 'active')->count(),
        ];

        return view('inventory.index', compact('items', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:100|unique:inventory_items',
            'unit'             => 'required|string|max:50',
            'quantity'         => 'required|numeric|min:0',
            'min_quantity'     => 'required|numeric|min:0',
            'cost_per_unit'    => 'nullable|numeric|min:0',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'category'         => 'nullable|string|max:100',
            'expiry_date'      => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
        ]);

        $item = InventoryItem::create($validated);

        // Log initial stock
        if ($validated['quantity'] > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id'           => auth()->id(),
                'type'              => 'in',
                'quantity'          => $validated['quantity'],
                'quantity_before'   => 0,
                'quantity_after'    => $validated['quantity'],
                'reason'            => 'Initial stock',
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventoryItem)
    {
        $transactions = $inventoryItem->transactions()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        return view('inventory.show', compact('inventoryItem', 'transactions'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('inventory.edit', compact('inventoryItem', 'suppliers'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:100|unique:inventory_items,sku,' . $inventoryItem->id,
            'unit'             => 'required|string|max:50',
            'min_quantity'     => 'required|numeric|min:0',
            'cost_per_unit'    => 'nullable|numeric|min:0',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'category'         => 'nullable|string|max:100',
            'expiry_date'      => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
        ]);

        $inventoryItem->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated.');
    }

    public function adjust(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'type'     => 'required|in:in,out,adjustment,waste',
            'quantity' => 'required|numeric|min:0.01',
            'reason'   => 'required|string|max:255',
        ]);

        $before = $inventoryItem->quantity;

        if ($request->type === 'in') {
            $after = $before + $request->quantity;
        } elseif (in_array($request->type, ['out', 'waste'])) {
            $after = max(0, $before - $request->quantity);
        } else {
            $after = $request->quantity;
        }

        $inventoryItem->update(['quantity' => $after]);

        InventoryTransaction::create([
            'inventory_item_id' => $inventoryItem->id,
            'user_id'           => auth()->id(),
            'type'              => $request->type,
            'quantity'          => $request->quantity,
            'quantity_before'   => $before,
            'quantity_after'    => $after,
            'reason'            => $request->reason,
        ]);

        return back()->with('success', 'Stock adjusted successfully.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->update(['is_active' => false]);
        return redirect()->route('inventory.index')
            ->with('success', 'Item deactivated.');
    }
}