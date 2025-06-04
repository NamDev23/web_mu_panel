<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class TAccountTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test accounts in t_account table
        $accounts = [
            [
                'UserName' => 'testuser',
                'Password' => md5('123456'),
                'Email' => 'testuser@game.com',
                'IPAddress' => '127.0.0.1',
                'UserID' => 'ZT00001',
                'groupid' => 0, // Normal user
                'Money' => 100000,
                'phone' => '0901234567',
            ],
            [
                'UserName' => 'gameadmin',
                'Password' => md5('admin123'),
                'Email' => 'gameadmin@game.com',
                'IPAddress' => '127.0.0.1',
                'UserID' => 'ZT00002',
                'groupid' => 1, // Admin
                'Money' => 999999,
                'phone' => '0901234568',
            ],
            [
                'UserName' => 'player1',
                'Password' => md5('player123'),
                'Email' => 'player1@game.com',
                'IPAddress' => '127.0.0.1',
                'UserID' => 'ZT00003',
                'groupid' => 0, // Normal user
                'Money' => 50000,
                'phone' => '0901234569',
            ],
        ];

        foreach ($accounts as $accountData) {
            // Check if account already exists
            $existing = Account::where('UserName', $accountData['UserName'])->first();
            if (!$existing) {
                Account::create($accountData);
                echo "Created account: " . $accountData['UserName'] . "\n";
            } else {
                echo "Account already exists: " . $accountData['UserName'] . "\n";
            }
        }

        echo "\nTest accounts created successfully!\n";
        echo "Test User - Username: testuser, Password: 123456\n";
        echo "Game Admin - Username: gameadmin, Password: admin123\n";
        echo "Player 1 - Username: player1, Password: player123\n";
    }
}
