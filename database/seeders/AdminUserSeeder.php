<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_users')->insert([
            [
                'username' => 'admin',
                'email' => 'admin@game.com',
                'password' => Hash::make('admin123'),
                'full_name' => 'Super Administrator',
                'role' => 'super_admin',
                'permissions' => json_encode(['all']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'moderator',
                'email' => 'mod@game.com',
                'password' => Hash::make('mod123'),
                'full_name' => 'Game Moderator',
                'role' => 'moderator',
                'permissions' => json_encode(['view_users', 'manage_giftcodes', 'view_logs']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
