<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAccount;
use App\Models\UserPaymentRequest;
use App\Models\UserTransactionLog;
use App\Models\CharacterServiceLog;
use App\Models\GiftcodeUsageLog;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = UserAccount::where('username', 'testuser')->first();
        
        if (!$user) {
            echo "❌ User 'testuser' not found. Run SimpleUserSeeder first.\n";
            return;
        }

        // Tạo payment requests mẫu
        $this->createPaymentRequests($user);
        
        // Tạo transaction logs mẫu
        $this->createTransactionLogs($user);
        
        // Tạo character service logs mẫu
        $this->createCharacterServiceLogs($user);
        
        // Tạo giftcode usage logs mẫu
        $this->createGiftcodeUsageLogs($user);

        echo "✅ Fake data created successfully!\n";
        echo "💰 Payment requests: 5 records\n";
        echo "📊 Transaction logs: 8 records\n";
        echo "🎮 Character service logs: 3 records\n";
        echo "🎁 Giftcode usage logs: 4 records\n";
    }

    private function createPaymentRequests($user)
    {
        // Completed card recharge
        UserPaymentRequest::create([
            'user_id' => $user->id,
            'payment_method' => 'card',
            'amount' => 100000,
            'coins_requested' => 10000,
            'status' => 'completed',
            'transaction_ref' => 'CARD_' . time() . '_1',
            'card_details' => [
                'type' => 'viettel',
                'serial' => '12345678901',
                'code' => '98765432109',
                'amount' => 100000
            ],
            'processed_by' => 1,
            'processed_at' => now()->subDays(5),
            'created_at' => now()->subDays(5)
        ]);

        // Pending bank transfer
        UserPaymentRequest::create([
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
            'amount' => 200000,
            'coins_requested' => 20000,
            'status' => 'pending',
            'transaction_ref' => 'BANK_' . time() . '_2',
            'qr_code_data' => [
                'bank_name' => 'Vietcombank',
                'account_number' => '1234567890',
                'amount' => 200000,
                'content' => 'NAPGAME ' . $user->id . ' ' . time()
            ],
            'created_at' => now()->subHours(2)
        ]);

        // Rejected card
        UserPaymentRequest::create([
            'user_id' => $user->id,
            'payment_method' => 'card',
            'amount' => 50000,
            'coins_requested' => 5000,
            'status' => 'rejected',
            'transaction_ref' => 'CARD_' . time() . '_3',
            'card_details' => [
                'type' => 'mobifone',
                'serial' => '11111111111',
                'code' => '22222222222',
                'amount' => 50000
            ],
            'admin_notes' => 'Thẻ cào không hợp lệ hoặc đã được sử dụng',
            'processed_by' => 1,
            'processed_at' => now()->subDays(3),
            'created_at' => now()->subDays(3)
        ]);

        // Processing bank transfer
        UserPaymentRequest::create([
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
            'amount' => 500000,
            'coins_requested' => 50000,
            'status' => 'processing',
            'transaction_ref' => 'BANK_' . time() . '_4',
            'qr_code_data' => [
                'bank_name' => 'Vietcombank',
                'account_number' => '1234567890',
                'amount' => 500000,
                'content' => 'NAPGAME ' . $user->id . ' ' . time()
            ],
            'admin_notes' => 'Đang xác minh giao dịch',
            'processed_by' => 1,
            'created_at' => now()->subHours(6)
        ]);

        // Completed PayPal
        UserPaymentRequest::create([
            'user_id' => $user->id,
            'payment_method' => 'paypal',
            'amount' => 300000,
            'coins_requested' => 30000,
            'status' => 'completed',
            'transaction_ref' => 'PP_' . time() . '_5',
            'gateway_response' => 'Payment completed successfully',
            'processed_by' => 1,
            'processed_at' => now()->subDays(7),
            'created_at' => now()->subDays(7)
        ]);
    }

    private function createTransactionLogs($user)
    {
        // Coin add from recharge
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'coin_add',
            'description' => 'Nạp thẻ cào Viettel 100,000đ',
            'coin_amount' => 10000,
            'coin_before' => 0,
            'coin_after' => 10000,
            'metadata' => ['payment_request_id' => 1],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(5)
        ]);

        // Service purchase - rename
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'service_purchase',
            'description' => 'Đổi tên nhân vật từ "OldWarrior" thành "WarriorKing"',
            'coin_amount' => -50000,
            'coin_before' => 60000,
            'coin_after' => 10000,
            'metadata' => [
                'service_type' => 'rename',
                'character_id' => 1,
                'old_name' => 'OldWarrior',
                'new_name' => 'WarriorKing'
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(4)
        ]);

        // Giftcode redeem
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'giftcode_redeem',
            'description' => 'Nhập giftcode: WELCOME2025',
            'coin_amount' => 1000,
            'coin_before' => 10000,
            'coin_after' => 11000,
            'metadata' => [
                'giftcode' => 'WELCOME2025',
                'rewards' => ['1000 Coin', '5 Jewel of Bless']
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(3)
        ]);

        // Service purchase - reset stats
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'service_purchase',
            'description' => 'Reset điểm kỹ năng cho nhân vật "MageQueen"',
            'coin_amount' => -30000,
            'coin_before' => 41000,
            'coin_after' => 11000,
            'metadata' => [
                'service_type' => 'reset_stats',
                'character_id' => 2,
                'character_name' => 'MageQueen'
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2)
        ]);

        // Transfer to game
        UserTransactionLog::create([
            'user_id' => $user->id,
            'type' => 'transfer_to_game',
            'description' => 'Chuyển coin sang tài khoản game',
            'coin_amount' => -1000,
            'coin_before' => 11000,
            'coin_after' => 10000,
            'metadata' => ['transferred_amount' => 1000],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(1)
        ]);
    }

    private function createCharacterServiceLogs($user)
    {
        // Completed rename
        CharacterServiceLog::create([
            'user_id' => $user->id,
            'character_id' => 1,
            'character_name' => 'WarriorKing',
            'service_type' => 'rename',
            'cost_coins' => 50000,
            'status' => 'completed',
            'service_data' => [
                'old_name' => 'OldWarrior',
                'new_name' => 'WarriorKing'
            ],
            'before_data' => ['name' => 'OldWarrior'],
            'after_data' => ['name' => 'WarriorKing'],
            'notes' => 'Đổi tên thành công',
            'processed_at' => now()->subDays(4),
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(4)
        ]);

        // Completed reset stats
        CharacterServiceLog::create([
            'user_id' => $user->id,
            'character_id' => 2,
            'character_name' => 'MageQueen',
            'service_type' => 'reset_stats',
            'cost_coins' => 30000,
            'status' => 'completed',
            'service_data' => ['reset_type' => 'stats'],
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
            'processed_at' => now()->subDays(2),
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2)
        ]);

        // Pending service
        CharacterServiceLog::create([
            'user_id' => $user->id,
            'character_id' => 3,
            'character_name' => 'ElfArcher',
            'service_type' => 'rename',
            'cost_coins' => 50000,
            'status' => 'pending',
            'service_data' => [
                'old_name' => 'ElfArcher',
                'new_name' => 'ElvenMaster'
            ],
            'before_data' => ['name' => 'ElfArcher'],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(3)
        ]);
    }

    private function createGiftcodeUsageLogs($user)
    {
        // Successful giftcode
        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'WELCOME2025',
            'giftcode_name' => 'Giftcode chào mừng năm mới',
            'status' => 'success',
            'rewards_received' => ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul'],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(3)
        ]);

        // Failed - already used
        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'WELCOME2025',
            'status' => 'used',
            'error_message' => 'Bạn đã sử dụng giftcode này rồi',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2)
        ]);

        // Failed - invalid
        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'INVALIDCODE',
            'status' => 'invalid',
            'error_message' => 'Mã giftcode không hợp lệ',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(1)
        ]);

        // Successful event code
        GiftcodeUsageLog::create([
            'user_id' => $user->id,
            'giftcode' => 'EVENT2025',
            'giftcode_name' => 'Sự kiện Tết Nguyên Đán',
            'status' => 'success',
            'rewards_received' => ['2000 Coin', '10 Jewel of Bless', '1 Box of Luck'],
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subHours(12)
        ]);
    }
}
