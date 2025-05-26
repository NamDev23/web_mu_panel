<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GameAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('game_accounts')->insert([
            [
                'username' => 'player123',
                'password' => Hash::make('123456'),
                'email' => 'player123@game.com',
                'phone' => '0901234567',
                'full_name' => 'Nguyễn Văn A',
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'banned_by' => null,
                'vip_level' => 3,
                'total_recharge' => 1500000.00,
                'current_balance' => 250000.00,
                'characters_count' => 2,
                'last_login_at' => now()->subHours(2),
                'last_login_ip' => '192.168.1.100',
                'is_verified' => true,
                'created_at' => now()->subDays(120),
                'updated_at' => now()->subHours(2),
            ],
            [
                'username' => 'gamer456',
                'password' => Hash::make('123456'),
                'email' => 'gamer456@game.com',
                'phone' => '0907654321',
                'full_name' => 'Trần Thị B',
                'status' => 'banned',
                'ban_reason' => 'Sử dụng hack, cheat trong game',
                'banned_at' => now()->subDays(5),
                'banned_by' => 1,
                'vip_level' => 1,
                'total_recharge' => 300000.00,
                'current_balance' => 50000.00,
                'characters_count' => 1,
                'last_login_at' => now()->subDays(5),
                'last_login_ip' => '192.168.1.101',
                'is_verified' => true,
                'created_at' => now()->subDays(90),
                'updated_at' => now()->subDays(5),
            ],
            [
                'username' => 'admin001',
                'password' => Hash::make('123456'),
                'email' => 'admin001@game.com',
                'phone' => '0909876543',
                'full_name' => 'Lê Văn C',
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'banned_by' => null,
                'vip_level' => 10,
                'total_recharge' => 5000000.00,
                'current_balance' => 1000000.00,
                'characters_count' => 5,
                'last_login_at' => now()->subMinutes(30),
                'last_login_ip' => '192.168.1.102',
                'is_verified' => true,
                'created_at' => now()->subDays(365),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'username' => 'newbie789',
                'password' => Hash::make('123456'),
                'email' => 'newbie789@game.com',
                'phone' => '0905555555',
                'full_name' => 'Phạm Văn D',
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'banned_by' => null,
                'vip_level' => 0,
                'total_recharge' => 0.00,
                'current_balance' => 10000.00,
                'characters_count' => 1,
                'last_login_at' => now()->subHours(1),
                'last_login_ip' => '192.168.1.103',
                'is_verified' => false,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subHours(1),
            ],
            [
                'username' => 'vipplayer',
                'password' => Hash::make('123456'),
                'email' => 'vipplayer@game.com',
                'phone' => '0908888888',
                'full_name' => 'Hoàng Thị E',
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'banned_by' => null,
                'vip_level' => 7,
                'total_recharge' => 3200000.00,
                'current_balance' => 500000.00,
                'characters_count' => 3,
                'last_login_at' => now()->subMinutes(15),
                'last_login_ip' => '192.168.1.104',
                'is_verified' => true,
                'created_at' => now()->subDays(200),
                'updated_at' => now()->subMinutes(15),
            ]
        ]);
    }
}
