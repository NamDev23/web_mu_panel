<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserAccount;
use App\Models\GameAccount;
use App\Models\WithdrawRequest;
use App\Models\UserTransactionLog;

class WithdrawTestSeeder extends Seeder
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

        // Tạo game accounts để test
        $this->createGameAccounts();
        
        // Tạo withdraw requests mẫu
        $this->createWithdrawRequests($user);

        echo "✅ Withdraw test data created successfully!\n";
        echo "🎮 Game accounts: 5 accounts\n";
        echo "💸 Withdraw requests: 6 records\n";
        echo "📊 Transaction logs: Updated\n";
        echo "🧪 Test withdraw with game usernames: testgamer1, testgamer2, testgamer3\n";
    }

    private function createGameAccounts()
    {
        // Xóa game accounts cũ nếu có
        GameAccount::whereIn('username', ['testgamer1', 'testgamer2', 'testgamer3', 'testgamer4', 'testgamer5'])->delete();

        $gameAccounts = [
            [
                'username' => 'testgamer1',
                'email' => 'testgamer1@example.com',
                'password' => Hash::make('gamepass123'),
                'current_balance' => 25000,
                'total_recharge' => 500000,
                'status' => 'active',
                'last_login_at' => now()->subHours(2),
                'created_at' => now()->subMonths(6),
                'updated_at' => now()
            ],
            [
                'username' => 'testgamer2',
                'email' => 'testgamer2@example.com',
                'password' => Hash::make('gamepass123'),
                'current_balance' => 150000,
                'total_recharge' => 2000000,
                'status' => 'active',
                'last_login_at' => now()->subMinutes(30),
                'created_at' => now()->subMonths(8),
                'updated_at' => now()
            ],
            [
                'username' => 'testgamer3',
                'email' => 'testgamer3@example.com',
                'password' => Hash::make('gamepass123'),
                'current_balance' => 75000,
                'total_recharge' => 1200000,
                'status' => 'active',
                'last_login_at' => now()->subHours(1),
                'created_at' => now()->subMonths(4),
                'updated_at' => now()
            ],
            [
                'username' => 'testgamer4',
                'email' => 'testgamer4@example.com',
                'password' => Hash::make('gamepass123'),
                'current_balance' => 5000,
                'total_recharge' => 100000,
                'status' => 'suspended',
                'last_login_at' => now()->subDays(10),
                'created_at' => now()->subMonths(2),
                'updated_at' => now()
            ],
            [
                'username' => 'testgamer5',
                'email' => 'testgamer5@example.com',
                'password' => Hash::make('gamepass123'),
                'current_balance' => 0,
                'total_recharge' => 50000,
                'status' => 'banned',
                'last_login_at' => now()->subDays(30),
                'created_at' => now()->subMonths(1),
                'updated_at' => now()
            ]
        ];

        foreach ($gameAccounts as $accountData) {
            GameAccount::create($accountData);
        }
    }

    private function createWithdrawRequests($user)
    {
        $gameAccount1 = GameAccount::where('username', 'testgamer1')->first();
        $gameAccount2 = GameAccount::where('username', 'testgamer2')->first();
        $gameAccount3 = GameAccount::where('username', 'testgamer3')->first();

        // Completed withdraw 1
        $withdraw1 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount1->id,
            'game_username' => 'testgamer1',
            'amount' => 50000,
            'status' => 'completed',
            'web_coins_before' => 60000,
            'web_coins_after' => 10000,
            'game_coins_before' => 25000,
            'game_coins_after' => 75000,
            'processed_at' => now()->subDays(5),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subDays(5)
        ]);

        // Completed withdraw 2
        $withdraw2 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount2->id,
            'game_username' => 'testgamer2',
            'amount' => 100000,
            'status' => 'completed',
            'web_coins_before' => 160000,
            'web_coins_after' => 60000,
            'game_coins_before' => 50000,
            'game_coins_after' => 150000,
            'processed_at' => now()->subDays(3),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subDays(3)
        ]);

        // Processing withdraw
        $withdraw3 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount3->id,
            'game_username' => 'testgamer3',
            'amount' => 25000,
            'status' => 'processing',
            'web_coins_before' => 35000,
            'web_coins_after' => 10000,
            'game_coins_before' => 50000,
            'game_coins_after' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subHours(2)
        ]);

        // Failed withdraw
        $withdraw4 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount1->id,
            'game_username' => 'testgamer1',
            'amount' => 75000,
            'status' => 'failed',
            'web_coins_before' => 85000,
            'web_coins_after' => 85000, // No change because failed
            'game_coins_before' => 75000,
            'game_coins_after' => 75000, // No change because failed
            'error_message' => 'Tài khoản game tạm thời không thể nhận coin',
            'processed_at' => now()->subDays(1),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subDays(1)
        ]);

        // Pending withdraw
        $withdraw5 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount2->id,
            'game_username' => 'testgamer2',
            'amount' => 30000,
            'status' => 'pending',
            'web_coins_before' => 40000,
            'web_coins_after' => null,
            'game_coins_before' => 150000,
            'game_coins_after' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subMinutes(30)
        ]);

        // Today's withdraw
        $withdraw6 = WithdrawRequest::create([
            'user_id' => $user->id,
            'game_account_id' => $gameAccount3->id,
            'game_username' => 'testgamer3',
            'amount' => 15000,
            'status' => 'completed',
            'web_coins_before' => 25000,
            'web_coins_after' => 10000,
            'game_coins_before' => 75000,
            'game_coins_after' => 90000,
            'processed_at' => now()->subHours(1),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'created_at' => now()->subHours(1)
        ]);

        // Create corresponding transaction logs
        $this->createTransactionLogs($user, [
            $withdraw1, $withdraw2, $withdraw3, $withdraw6 // Only successful/processing ones
        ]);
    }

    private function createTransactionLogs($user, $withdraws)
    {
        foreach ($withdraws as $withdraw) {
            if (in_array($withdraw->status, ['completed', 'processing'])) {
                UserTransactionLog::create([
                    'user_id' => $user->id,
                    'type' => 'transfer_to_game',
                    'description' => "Rút {$withdraw->amount} coin sang tài khoản game: {$withdraw->game_username}",
                    'coin_amount' => -$withdraw->amount,
                    'coin_before' => $withdraw->web_coins_before,
                    'coin_after' => $withdraw->web_coins_after ?? $withdraw->web_coins_before - $withdraw->amount,
                    'metadata' => [
                        'withdraw_request_id' => $withdraw->id,
                        'game_username' => $withdraw->game_username,
                        'game_account_id' => $withdraw->game_account_id,
                        'game_coins_before' => $withdraw->game_coins_before,
                        'game_coins_after' => $withdraw->game_coins_after
                    ],
                    'reference_type' => WithdrawRequest::class,
                    'reference_id' => $withdraw->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 Test Browser',
                    'created_at' => $withdraw->created_at
                ]);
            }
        }
    }
}
