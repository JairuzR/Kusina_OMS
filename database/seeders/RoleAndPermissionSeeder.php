<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Dashboard
            'view dashboard',

            // User management
            'view users', 'create users', 'edit users', 'delete users',

            // Menu management
            'view menu', 'create menu', 'edit menu', 'delete menu',

            // Table management
            'view tables', 'create tables', 'edit tables', 'delete tables',

            // Order management
            'view orders', 'create orders', 'edit orders', 'delete orders',
            'manage order status',

            // Kitchen
            'view kitchen', 'update kitchen orders',

            // Inventory
            'view inventory', 'create inventory', 'edit inventory', 'delete inventory',

            // Reservations
            'view reservations', 'create reservations',
            'edit reservations', 'delete reservations',

            // Payments & Billing
            'view payments', 'process payments', 'apply discounts',

            // Reports
            'view reports', 'export reports',

            // Audit logs
            'view audit logs',

            // Settings
            'view settings', 'edit settings',

            // Backup
            'manage backups',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin — full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Manager — everything except settings and user management
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view dashboard',
            'view users',
            'view menu', 'create menu', 'edit menu', 'delete menu',
            'view tables', 'create tables', 'edit tables', 'delete tables',
            'view orders', 'create orders', 'edit orders', 'delete orders',
            'manage order status',
            'view kitchen', 'update kitchen orders',
            'view inventory', 'create inventory', 'edit inventory', 'delete inventory',
            'view reservations', 'create reservations', 'edit reservations', 'delete reservations',
            'view payments', 'process payments', 'apply discounts',
            'view reports', 'export reports',
            'view audit logs',
        ]);

        // Cashier
        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->givePermissionTo([
            'view dashboard',
            'view orders', 'edit orders', 'manage order status',
            'view payments', 'process payments', 'apply discounts',
            'view reservations', 'create reservations',
        ]);

        // Waiter
        $waiter = Role::firstOrCreate(['name' => 'waiter']);
        $waiter->givePermissionTo([
            'view dashboard',
            'view tables',
            'view orders', 'create orders', 'edit orders', 'manage order status',
            'view menu',
            'view reservations', 'create reservations',
        ]);

        // Kitchen Staff
        $kitchen = Role::firstOrCreate(['name' => 'kitchen_staff']);
        $kitchen->givePermissionTo([
            'view kitchen',
            'update kitchen orders',
            'view menu',
            'view inventory',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}