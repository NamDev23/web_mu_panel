<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Account;
use App\Character;
use DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        try {
            // Total accounts
            $totalAccounts = Account::count();
            
            // Active accounts (logged in last 30 days)
            $activeAccounts = Account::where('last_login', '>=', now()->subDays(30))->count();
            
            // New accounts today
            $newAccountsToday = Account::whereDate('created_at', today())->count();
            
            // Banned accounts
            $bannedAccounts = Account::where('groupid', 99)->count();
            
            // Total characters
            $totalCharacters = Character::count();
            
            // Online players (mock data)
            $onlinePlayers = rand(50, 200);
            
            // Revenue this month (mock data)
            $monthlyRevenue = rand(10000000, 50000000);
            
            // Top level characters
            $topCharacters = Character::with('account:UserID,UserName')
                                   ->orderBy('level', 'desc')
                                   ->limit(10)
                                   ->get()
                                   ->map(function($char) {
                                       return [
                                           'rname' => $char->rname,
                                           'level' => $char->level ?? 1,
                                           'account_name' => $char->account->UserName ?? 'Unknown',
                                           'server' => 'Server ' . ($char->serverid ?? 1)
                                       ];
                                   });
            
            // Recent registrations
            $recentRegistrations = Account::orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get(['UserID', 'UserName', 'Email', 'created_at', 'Money'])
                                        ->map(function($account) {
                                            return [
                                                'UserID' => $account->UserID,
                                                'UserName' => $account->UserName,
                                                'Email' => $account->Email,
                                                'Money' => $account->Money,
                                                'created_at' => $account->created_at
                                            ];
                                        });
            
            // Server statistics (mock data)
            $serverStats = [
                [
                    'id' => 1,
                    'name' => 'Server 1',
                    'online' => rand(30, 80),
                    'max_online' => 100,
                    'status' => 'online'
                ],
                [
                    'id' => 2,
                    'name' => 'Server 2', 
                    'online' => rand(20, 60),
                    'max_online' => 100,
                    'status' => 'online'
                ],
                [
                    'id' => 3,
                    'name' => 'Server 3',
                    'online' => rand(10, 40),
                    'max_online' => 100,
                    'status' => 'maintenance'
                ]
            ];
            
            // Activity chart data (last 7 days)
            $activityData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $activityData[] = [
                    'date' => $date->format('Y-m-d'),
                    'logins' => rand(20, 100),
                    'registrations' => rand(5, 25),
                    'revenue' => rand(500000, 2000000)
                ];
            }
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'total_accounts' => $totalAccounts,
                    'active_accounts' => $activeAccounts,
                    'new_accounts_today' => $newAccountsToday,
                    'banned_accounts' => $bannedAccounts,
                    'total_characters' => $totalCharacters,
                    'online_players' => $onlinePlayers,
                    'monthly_revenue' => $monthlyRevenue
                ],
                'top_characters' => $topCharacters,
                'recent_registrations' => $recentRegistrations,
                'server_stats' => $serverStats,
                'activity_data' => $activityData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
