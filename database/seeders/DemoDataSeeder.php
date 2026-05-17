<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTables();
        $this->seedMenu();
        $this->seedSuppliers();
        $this->seedInventory();

        $this->command->info('Demo data seeded successfully!');
    }

    private function seedTables(): void
    {
        $waiter = User::role('waiter')->first();

        $tables = [
            ['name' => 'Table 1', 'capacity' => 4, 'location' => 'indoor', 'status' => 'available'],
            ['name' => 'Table 2', 'capacity' => 4, 'location' => 'indoor', 'status' => 'available'],
            ['name' => 'Table 3', 'capacity' => 6, 'location' => 'indoor', 'status' => 'available'],
            ['name' => 'Table 4', 'capacity' => 2, 'location' => 'outdoor', 'status' => 'available'],
            ['name' => 'Table 5', 'capacity' => 2, 'location' => 'outdoor', 'status' => 'available'],
            ['name' => 'VIP 1',   'capacity' => 8, 'location' => 'vip',     'status' => 'available'],
            ['name' => 'VIP 2',   'capacity' => 10,'location' => 'vip',     'status' => 'available'],
            ['name' => 'Bar 1',   'capacity' => 2, 'location' => 'bar',     'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::firstOrCreate(
                ['name' => $table['name']],
                array_merge($table, [
                    'assigned_waiter_id' => $waiter?->id,
                    'is_active'          => true,
                ])
            );
        }

        $this->command->info('Tables seeded.');
    }

    private function seedMenu(): void
    {
        $categories = [
            [
                'name'  => 'Appetizers',
                'slug'  => 'appetizers',
                'items' => [
                    ['name' => 'Lumpiang Shanghai',    'price' => 120, 'prep' => 10],
                    ['name' => 'Calamares',            'price' => 150, 'prep' => 12],
                    ['name' => 'Tokwa at Baboy',       'price' => 130, 'prep' => 8],
                ],
            ],
            [
                'name'  => 'Main Course',
                'slug'  => 'main-course',
                'items' => [
                    ['name' => 'Crispy Pata',          'price' => 480, 'prep' => 45],
                    ['name' => 'Kare-Kare',            'price' => 380, 'prep' => 30],
                    ['name' => 'Sinigang na Baboy',    'price' => 320, 'prep' => 25],
                    ['name' => 'Adobong Manok',        'price' => 280, 'prep' => 20],
                    ['name' => 'Bistek Tagalog',       'price' => 300, 'prep' => 20],
                ],
            ],
            [
                'name'  => 'Seafood',
                'slug'  => 'seafood',
                'items' => [
                    ['name' => 'Grilled Bangus',       'price' => 250, 'prep' => 20],
                    ['name' => 'Sinigang na Hipon',    'price' => 350, 'prep' => 25],
                    ['name' => 'Pinangat na Isda',     'price' => 220, 'prep' => 20],
                ],
            ],
            [
                'name'  => 'Rice & Noodles',
                'slug'  => 'rice-noodles',
                'items' => [
                    ['name' => 'Steamed Rice',         'price' => 35,  'prep' => 5],
                    ['name' => 'Garlic Fried Rice',    'price' => 55,  'prep' => 8],
                    ['name' => 'Pancit Canton',        'price' => 180, 'prep' => 15],
                    ['name' => 'Palabok',              'price' => 160, 'prep' => 15],
                ],
            ],
            [
                'name'  => 'Desserts',
                'slug'  => 'desserts',
                'items' => [
                    ['name' => 'Halo-Halo',            'price' => 120, 'prep' => 8],
                    ['name' => 'Leche Flan',           'price' => 80,  'prep' => 5],
                    ['name' => 'Buko Pandan',          'price' => 90,  'prep' => 5],
                ],
            ],
            [
                'name'  => 'Drinks',
                'slug'  => 'drinks',
                'items' => [
                    ['name' => 'Iced Tea',             'price' => 60,  'prep' => 3],
                    ['name' => 'Bottled Water',        'price' => 35,  'prep' => 1],
                    ['name' => 'Fresh Buko Juice',     'price' => 80,  'prep' => 5],
                    ['name' => 'Sago at Gulaman',      'price' => 55,  'prep' => 3],
                    ['name' => 'Softdrinks',           'price' => 45,  'prep' => 1],
                ],
            ],
        ];

        foreach ($categories as $index => $cat) {
            $category = MenuCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name'       => $cat['name'],
                    'slug'       => $cat['slug'],
                    'is_active'  => true,
                    'sort_order' => $index,
                ]
            );

            foreach ($cat['items'] as $itemIndex => $item) {
                MenuItem::firstOrCreate(
                    ['name' => $item['name'], 'menu_category_id' => $category->id],
                    [
                        'menu_category_id' => $category->id,
                        'name'             => $item['name'],
                        'slug'             => \Illuminate\Support\Str::slug($item['name']),
                        'price'            => $item['price'],
                        'cost_price'       => $item['price'] * 0.4,
                        'preparation_time' => $item['prep'],
                        'is_available'     => true,
                        'is_featured'      => $itemIndex === 0,
                        'sort_order'       => $itemIndex,
                    ]
                );
            }
        }

        $this->command->info('Menu seeded.');
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            [
                'name'           => 'Fresh Farms PH',
                'contact_person' => 'Juan dela Cruz',
                'phone'          => '+63 912 111 1111',
                'email'          => 'fresh@farms.ph',
                'address'        => 'Cagayan de Oro City',
                'status'         => 'active',
            ],
            [
                'name'           => 'Metro Meat Supply',
                'contact_person' => 'Maria Santos',
                'phone'          => '+63 912 222 2222',
                'email'          => 'metro@meatsupply.com',
                'address'        => 'Cagayan de Oro City',
                'status'         => 'active',
            ],
            [
                'name'           => 'SeaFresh Distributors',
                'contact_person' => 'Pedro Reyes',
                'phone'          => '+63 912 333 3333',
                'email'          => 'seafresh@dist.com',
                'address'        => 'Cagayan de Oro City',
                'status'         => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }

        $this->command->info('Suppliers seeded.');
    }

    private function seedInventory(): void
    {
        $freshFarms  = Supplier::where('name', 'Fresh Farms PH')->first();
        $metroMeat   = Supplier::where('name', 'Metro Meat Supply')->first();
        $seafresh    = Supplier::where('name', 'SeaFresh Distributors')->first();

        $items = [
            // Ingredients
            ['name' => 'Pork Belly',        'unit' => 'kg',      'qty' => 15,  'min' => 5,  'cost' => 280, 'category' => 'Meat',       'supplier' => $metroMeat],
            ['name' => 'Chicken Thighs',    'unit' => 'kg',      'qty' => 12,  'min' => 4,  'cost' => 220, 'category' => 'Meat',       'supplier' => $metroMeat],
            ['name' => 'Beef Sirloin',      'unit' => 'kg',      'qty' => 8,   'min' => 3,  'cost' => 450, 'category' => 'Meat',       'supplier' => $metroMeat],
            ['name' => 'Bangus (Milkfish)', 'unit' => 'kg',      'qty' => 10,  'min' => 3,  'cost' => 180, 'category' => 'Seafood',    'supplier' => $seafresh],
            ['name' => 'Hipon (Shrimp)',    'unit' => 'kg',      'qty' => 6,   'min' => 2,  'cost' => 320, 'category' => 'Seafood',    'supplier' => $seafresh],
            ['name' => 'White Rice',        'unit' => 'kg',      'qty' => 50,  'min' => 15, 'cost' => 55,  'category' => 'Grains',     'supplier' => $freshFarms],
            ['name' => 'Garlic',            'unit' => 'kg',      'qty' => 3,   'min' => 1,  'cost' => 120, 'category' => 'Vegetables', 'supplier' => $freshFarms],
            ['name' => 'Onion',             'unit' => 'kg',      'qty' => 4,   'min' => 1,  'cost' => 80,  'category' => 'Vegetables', 'supplier' => $freshFarms],
            ['name' => 'Tomato',            'unit' => 'kg',      'qty' => 2,   'min' => 1,  'cost' => 60,  'category' => 'Vegetables', 'supplier' => $freshFarms],
            ['name' => 'Cooking Oil',       'unit' => 'liters',  'qty' => 8,   'min' => 3,  'cost' => 95,  'category' => 'Condiments', 'supplier' => $freshFarms],
            ['name' => 'Soy Sauce',         'unit' => 'liters',  'qty' => 5,   'min' => 2,  'cost' => 65,  'category' => 'Condiments', 'supplier' => $freshFarms],
            ['name' => 'Vinegar',           'unit' => 'liters',  'qty' => 4,   'min' => 2,  'cost' => 45,  'category' => 'Condiments', 'supplier' => $freshFarms],
            // Supplies
            ['name' => 'Tissue Paper',      'unit' => 'packs',   'qty' => 20,  'min' => 5,  'cost' => 35,  'category' => 'Supplies',   'supplier' => null],
            ['name' => 'Disposable Cups',   'unit' => 'pcs',     'qty' => 200, 'min' => 50, 'cost' => 3,   'category' => 'Supplies',   'supplier' => null],
            ['name' => 'Straws',            'unit' => 'pcs',     'qty' => 500, 'min' => 100,'cost' => 0.5, 'category' => 'Supplies',   'supplier' => null],
        ];

        $admin = User::role('admin')->first();

        foreach ($items as $item) {
            $inventoryItem = InventoryItem::firstOrCreate(
                ['name' => $item['name']],
                [
                    'supplier_id'   => $item['supplier']?->id,
                    'name'          => $item['name'],
                    'unit'          => $item['unit'],
                    'quantity'      => $item['qty'],
                    'min_quantity'  => $item['min'],
                    'cost_per_unit' => $item['cost'],
                    'category'      => $item['category'],
                    'is_active'     => true,
                ]
            );

            // Log initial stock transaction
            if ($inventoryItem->wasRecentlyCreated) {
                InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'user_id'           => $admin?->id,
                    'type'              => 'in',
                    'quantity'          => $item['qty'],
                    'quantity_before'   => 0,
                    'quantity_after'    => $item['qty'],
                    'reason'            => 'Initial stock',
                ]);
            }
        }

        $this->command->info('Inventory seeded.');
    }
}