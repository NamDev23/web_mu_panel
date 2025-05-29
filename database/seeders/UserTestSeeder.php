<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAccount;
use App\Models\UserCoinBalance;
use App\Models\GameAccount;
use App\Models\UserTransactionLog;
use App\Models\CharacterServiceLog;
use App\Models\GiftcodeUsageLog;

class UserTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo game account test
        $gameAccount = GameAccount::create([
            'username' => 'testgamer',
            'email' => 'testgamer@example.com',
            'password' => Hash::make('gamepass123'),
            'current_balance' => 50000,
            'total_recharge' => 1000000,
            'status' => 'active',
            'last_login_at' => now(),
            'created_at' => now()->subMonths(6),
            'updated_at' => now()
        ]);

        // Tạo user account test
        $user = UserAccount::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('123456'),
            'phone' => '0123456789',
            'game_account_id' => $gameAccount->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now()->subMonths(3),
            'updated_at' => now()
        ]);

        // Tạo coin balance
        UserCoinBalance::create([
            'user_id' => $user->id,
            'web_coins' => 75000,
            'game_coins' => 25000,
            'total_recharged' => 500000,
            'last_recharge_at' => now()->subDays(5)
        ]);

        // Tạo user thứ 2 không có game account
        $user2 = UserAccount::create([
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => Hash::make('123456'),
            'phone' => '0987654321',
            'game_account_id' => null,
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now()->subWeeks(2),
            'updated_at' => now()
        ]);

        UserCoinBalance::create([
            'user_id' => $user2->id,
            'web_coins' => 5000,
            'game_coins' => 0,
            'total_recharged' => 50000,
            'last_recharge_at' => now()->subDays(1)
        ]);

        // Tạo một số transaction logs mẫu
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'coin_add',
            'description' => 'Nạp thẻ cào Viettel 100,000đ',
            'coin_amount' => 10000,
            'coin_before' => 65000,
            'coin_after' => 75000,
            'metadata' => [
                'card_type' => 'viettel',
                'card_amount' => 100000,
                'card_serial' => '12345678901',
                'card_code' => '98765432109'
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(5)
        ]);

        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'service_purchase',
            'description' => 'Đổi tên nhân vật từ "OldName" thành "WarriorKing"',
            'coin_amount' => -50000,
            'coin_before' => 75000,
            'coin_after' => 25000,
            'metadata' => [
                'service_type' => 'rename',
                'character_id' => 1,
                'old_name' => 'OldName',
                'new_name' => 'WarriorKing'
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(3)
        ]);

        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'giftcode_redeem',
            'description' => 'Nhập giftcode: WELCOME2025',
            'coin_amount' => 1000,
            'coin_before' => 25000,
            'coin_after' => 26000,
            'metadata' => [
                'giftcode' => 'WELCOME2025',
                'rewards' => ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul']
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2)
        ]);

        // Tạo character service logs mẫu
        CharacterServiceLog::create([
            'user_id' => $user->id,
            'character_id' => 1,
            'character_name' => 'WarriorKing',
            'service_type' => 'rename',
            'cost_coins' => 50000,
            'status' => 'completed',
            'service_data' => [
                'old_name' => 'OldName',
                'new_name' => 'WarriorKing'
            ],
            'before_data' => [
                'name' => 'OldName'
            ],
            'after_data' => [
                'name' => 'WarriorKing'
            ],
            'notes' => 'Đổi tên thành công',
            'processed_at' => now()->subDays(3),
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(3)
        ]);

        CharacterServiceLog::create([
            'user_id' => $user->id,
            'character_id' => 2,
            'character_name' => 'MageQueen',
            'service_type' => 'reset_stats',
            'cost_coins' => 30000,
            'status' => 'completed',
            'service_data' => [
                'reset_type' => 'stats'
            ],
            'before_data' => [
                'str' => 150,
                'agi' => 120,
                'vit' => 100,
                'ene' => 80
            ],
            'after_data' => [
                'str' => 0,
                'agi' => 0,
                'vit' => 0,
                'ene' => 0,
                'available_points' => 450
            ],
            'notes' => 'Reset điểm kỹ năng thành công',
            'processed_at' => now()->subDays(1),
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(1)
        ]);

        // Tạo giftcode usage logs mẫu
        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'WELCOME2025',
            'giftcode_name' => 'Giftcode chào mừng năm mới',
            'status' => 'success',
            'rewards_received' => ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul'],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2)
        ]);

        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'INVALIDCODE',
            'status' => 'invalid',
            'error_message' => 'Mã giftcode không hợp lệ',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(1)
        ]);

        echo "✅ Test users created successfully!\n";
        echo "👤 User 1: testuser / 123456 (có game account)\n";
        echo "👤 User 2: newuser / 123456 (chưa có game account)\n";
        echo "💰 Coin balance: 75,000 web coins\n";
        echo "📊 Sample transaction logs created\n";
        echo "🎮 Sample character service logs created\n";
        echo "🎁 Sample giftcode usage logs created\n";
    }
}
