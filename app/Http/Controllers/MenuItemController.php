<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function create()
    {
        $categories = MenuCategory::where('is_active', true)->orderBy('name')->get();
        return view('menu.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_category_id'  => 'required|exists:menu_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'preparation_time'  => 'nullable|integer|min:1',
            'calories'          => 'nullable|integer|min:0',
            'is_available'      => 'boolean',
            'is_featured'       => 'boolean',
            'image'             => 'nullable|image|max:2048',
        ]);

        $validated['slug']         = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured']  = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu/items', 'public');
        }

        MenuItem::create($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        return view('menu.items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = MenuCategory::where('is_active', true)->orderBy('name')->get();
        return view('menu.items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'menu_category_id'  => 'required|exists:menu_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'preparation_time'  => 'nullable|integer|min:1',
            'calories'          => 'nullable|integer|min:0',
            'is_available'      => 'boolean',
            'is_featured'       => 'boolean',
            'image'             => 'nullable|image|max:2048',
        ]);

        $validated['slug']         = Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured']  = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu/items', 'public');
        }

        $menuItem->update($validated);

        return redirect()->route('menu.index')
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('menu.index')
            ->with('success', 'Menu item deleted successfully.');
    }

    public function toggle(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return back()->with('success', 'Item availability updated.');
    }
}