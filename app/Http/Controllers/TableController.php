<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with('waiter')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $waiters = User::role('waiter')
            ->where('status', 'active')
            ->get();

        $stats = [
            'total'          => $tables->count(),
            'available'      => $tables->where('status', 'available')->count(),
            'occupied'       => $tables->where('status', 'occupied')->count(),
            'reserved'       => $tables->where('status', 'reserved')->count(),
            'needs_cleaning' => $tables->where('status', 'needs_cleaning')->count(),
        ];

        return view('tables.index', compact('tables', 'waiters', 'stats'));
    }

    public function create()
    {
        $waiters = User::role('waiter')
            ->where('status', 'active')
            ->get();

        return view('tables.create', compact('waiters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'capacity'           => 'required|integer|min:1|max:50',
            'location'           => 'nullable|string|max:255',
            'assigned_waiter_id' => 'nullable|exists:users,id',
        ]);

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit(Table $table)
    {
        $waiters = User::role('waiter')
            ->where('status', 'active')
            ->get();

        return view('tables.edit', compact('table', 'waiters'));
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'capacity'           => 'required|integer|min:1|max:50',
            'location'           => 'nullable|string|max:255',
            'assigned_waiter_id' => 'nullable|exists:users,id',
            'is_active'          => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        $table->update(['is_active' => false]);

        return redirect()->route('tables.index')
            ->with('success', 'Table deactivated successfully.');
    }

    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,needs_cleaning',
        ]);

        $table->update(['status' => $request->status]);

        return back()->with('success', 'Table status updated.');
    }
}