<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\UserCoinBalance;

class SimpleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Xóa user cũ nếu có
        DB::table('user_coin_balance')->where('user_id', '<=', 10)->delete();
        DB::table('user_accounts')->where('id', '<=', 10)->delete();

        // Tạo user test đơn giản
        $user = UserAccount::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('123456'),
            'phone' => '0123456789',
            'game_account_id' => null, // Không liên kết game account
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Tạo coin balance
        UserCoinBalance::create([
            'user_id' => $user->id,
            'web_coins' => 10000,
            'game_coins' => 0,
            'total_recharged' => 0,
            'last_recharge_at' => null
        ]);

        // Tạo user thứ 2
        $user2 = UserAccount::create([
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => Hash::make('123456'),
            'phone' => '0987654321',
            'game_account_id' => null,
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        UserCoinBalance::create([
            'user_id' => $user2->id,
            'web_coins' => 5000,
            'game_coins' => 0,
            'total_recharged' => 0,
            'last_recharge_at' => null
        ]);

        echo "✅ Simple test users created!\n";
        echo "👤 testuser / 123456 (10,000 coins)\n";
        echo "👤 newuser / 123456 (5,000 coins)\n";
        echo "🌐 Test at: http://localhost:8000/user/login\n";
    }
}
