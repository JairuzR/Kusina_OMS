<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@kusinaoms.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'status'   => 'active',
                'phone'    => '+63 912 000 0001',
            ],
            [
                'name'     => 'Manager User',
                'email'    => 'manager@kusinaoms.com',
                'password' => Hash::make('password'),
                'role'     => 'manager',
                'status'   => 'active',
                'phone'    => '+63 912 000 0002',
            ],
            [
                'name'     => 'Cashier User',
                'email'    => 'cashier@kusinaoms.com',
                'password' => Hash::make('password'),
                'role'     => 'cashier',
                'status'   => 'active',
                'phone'    => '+63 912 000 0003',
            ],
            [
                'name'     => 'Waiter User',
                'email'    => 'waiter@kusinaoms.com',
                'password' => Hash::make('password'),
                'role'     => 'waiter',
                'status'   => 'active',
                'phone'    => '+63 912 000 0004',
            ],
            [
                'name'     => 'Kitchen Staff',
                'email'    => 'kitchen@kusinaoms.com',
                'password' => Hash::make('password'),
                'role'     => 'kitchen_staff',
                'status'   => 'active',
                'phone'    => '+63 912 000 0005',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->assignRole($userData['role']);
        }

        $this->command->info('Admin and staff users seeded.');
    }
}