<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    // ── Menu Items ────────────────────────────────────────────────────────────

    public function downloadMenuTemplate()
    {
        $filename = 'menu_items_template.csv';
        $headers  = ['name', 'category', 'description', 'price', 'cost_price', 'preparation_time', 'calories', 'is_available', 'is_featured'];

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            // Example row
            fputcsv($handle, ['Adobo', 'Main Course', 'Classic Filipino dish', '150.00', '60.00', '15', '450', 'true', 'false']);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function importMenu(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $handle   = fopen($request->file('file')->getRealPath(), 'r');
        $headers  = array_map('trim', fgetcsv($handle));

        $required = ['name', 'category', 'price'];
        $missing  = array_diff($required, $headers);

        if (!empty($missing)) {
            return back()->with('error', 'CSV is missing required columns: ' . implode(', ', $missing));
        }

        $imported = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) < count($headers)) {
                $errors[] = "Row {$row}: not enough columns.";
                continue;
            }

            $record = array_combine($headers, $data);

            // Validate required fields
            if (empty(trim($record['name']))) {
                $errors[] = "Row {$row}: name is required.";
                continue;
            }
            if (!is_numeric($record['price']) || $record['price'] < 0) {
                $errors[] = "Row {$row}: price must be a positive number.";
                continue;
            }

            // Resolve category
            $category = MenuCategory::firstOrCreate(
                ['name' => trim($record['category'])],
                ['slug' => Str::slug(trim($record['category'])), 'is_active' => true]
            );

            // Duplicate check by name + category
            if (MenuItem::where('name', trim($record['name']))->where('menu_category_id', $category->id)->exists()) {
                $errors[] = "Row {$row}: \"{$record['name']}\" already exists in \"{$record['category']}\".";
                continue;
            }

            MenuItem::create([
                'menu_category_id' => $category->id,
                'name'             => trim($record['name']),
                'slug'             => Str::slug(trim($record['name'])) . '-' . Str::random(4),
                'description'      => $record['description'] ?? null,
                'price'            => (float) $record['price'],
                'cost_price'       => isset($record['cost_price']) && is_numeric($record['cost_price']) ? (float) $record['cost_price'] : null,
                'preparation_time' => isset($record['preparation_time']) && is_numeric($record['preparation_time']) ? (int) $record['preparation_time'] : 15,
                'calories'         => isset($record['calories']) && is_numeric($record['calories']) ? (int) $record['calories'] : null,
                'is_available'     => strtolower($record['is_available'] ?? 'true') === 'true',
                'is_featured'      => strtolower($record['is_featured'] ?? 'false') === 'true',
            ]);

            $imported++;
        }

        fclose($handle);

        AuditLog::record('created', 'menu', description: "Imported {$imported} menu items via CSV");

        $message = "Imported {$imported} menu item(s) successfully.";
        if (!empty($errors)) {
            return back()
                ->with('import_errors', $errors)
                ->with('warning', $message . ' ' . count($errors) . ' row(s) had errors.');
        }

        return back()->with('success', $message);
    }

    // ── Inventory ─────────────────────────────────────────────────────────────

    public function downloadInventoryTemplate()
    {
        $filename = 'inventory_template.csv';
        $headers  = ['name', 'sku', 'category', 'unit', 'quantity', 'min_quantity', 'cost_per_unit', 'supplier', 'storage_location', 'expiry_date'];

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fputcsv($handle, ['Rice', 'RICE-001', 'Dry Goods', 'kg', '50', '10', '45.00', 'AgriSupply Co.', 'Dry Storage', '2025-12-31']);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function importInventory(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $handle  = fopen($request->file('file')->getRealPath(), 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $required = ['name', 'unit', 'quantity'];
        $missing  = array_diff($required, $headers);

        if (!empty($missing)) {
            return back()->with('error', 'CSV is missing required columns: ' . implode(', ', $missing));
        }

        $imported = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) < count($headers)) {
                $errors[] = "Row {$row}: not enough columns.";
                continue;
            }

            $record = array_combine($headers, $data);

            if (empty(trim($record['name']))) {
                $errors[] = "Row {$row}: name is required.";
                continue;
            }
            if (!is_numeric($record['quantity']) || $record['quantity'] < 0) {
                $errors[] = "Row {$row}: quantity must be a positive number.";
                continue;
            }

            // Duplicate check by SKU or name
            $sku = !empty($record['sku']) ? trim($record['sku']) : null;
            if ($sku && InventoryItem::where('sku', $sku)->exists()) {
                $errors[] = "Row {$row}: SKU \"{$sku}\" already exists.";
                continue;
            }
            if (InventoryItem::where('name', trim($record['name']))->exists()) {
                $errors[] = "Row {$row}: \"{$record['name']}\" already exists.";
                continue;
            }

            // Resolve supplier
            $supplierId = null;
            if (!empty($record['supplier'])) {
                $supplier   = Supplier::firstOrCreate(
                    ['name' => trim($record['supplier'])],
                    ['status' => 'active']
                );
                $supplierId = $supplier->id;
            }

            InventoryItem::create([
                'name'             => trim($record['name']),
                'sku'              => $sku,
                'category'         => $record['category'] ?? null,
                'unit'             => trim($record['unit']),
                'quantity'         => (float) $record['quantity'],
                'min_quantity'     => isset($record['min_quantity']) && is_numeric($record['min_quantity']) ? (float) $record['min_quantity'] : 0,
                'cost_per_unit'    => isset($record['cost_per_unit']) && is_numeric($record['cost_per_unit']) ? (float) $record['cost_per_unit'] : null,
                'supplier_id'      => $supplierId,
                'storage_location' => $record['storage_location'] ?? null,
                'expiry_date'      => !empty($record['expiry_date']) ? $record['expiry_date'] : null,
                'is_active'        => true,
            ]);

            $imported++;
        }

        fclose($handle);

        AuditLog::record('created', 'inventory', description: "Imported {$imported} inventory items via CSV");

        $message = "Imported {$imported} inventory item(s) successfully.";
        if (!empty($errors)) {
            return back()
                ->with('import_errors', $errors)
                ->with('warning', $message . ' ' . count($errors) . ' row(s) had errors.');
        }

        return back()->with('success', $message);
    }
}