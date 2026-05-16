<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('restaurants')->insert([
            'name'         => 'KusinaOMS Restaurant',
            'address'      => '123 Main Street, Cagayan de Oro City',
            'phone'        => '+63 912 345 6789',
            'email'        => 'info@kusinaoms.com',
            'tax_rate'     => 12.00,
            'currency'     => 'PHP',
            'timezone'     => 'Asia/Manila',
            'opening_time' => '08:00:00',
            'closing_time' => '22:00:00',
            'receipt_footer' => 'Thank you for dining with us!',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Seed default settings
        $settings = [
            ['key' => 'restaurant_name',   'value' => 'KusinaOMS Restaurant', 'group' => 'general',  'type' => 'string',  'is_public' => true],
            ['key' => 'tax_rate',          'value' => '12',                   'group' => 'billing',  'type' => 'integer', 'is_public' => false],
            ['key' => 'currency',          'value' => 'PHP',                  'group' => 'billing',  'type' => 'string',  'is_public' => true],
            ['key' => 'session_timeout',   'value' => '120',                  'group' => 'security', 'type' => 'integer', 'is_public' => false],
            ['key' => 'max_login_attempts','value' => '5',                    'group' => 'security', 'type' => 'integer', 'is_public' => false],
            ['key' => 'backup_schedule',   'value' => 'weekly',               'group' => 'backup',   'type' => 'string',  'is_public' => false],
            ['key' => 'low_stock_alert',   'value' => 'true',                 'group' => 'alerts',   'type' => 'boolean', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Restaurant and settings seeded.');
    }
}