<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\UserPaymentRequest;
use App\Models\UserBattlePass;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with(['coinBalance', 'gameAccount'])->find($user['id']);

        // Get user statistics
        $stats = $this->getUserStats($userAccount);

        // Get recent activities
        $recentPayments = UserPaymentRequest::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get monthly cards info (if linked to game account)
        $monthlyCards = collect(); // Use empty collection instead of array
        if ($userAccount->game_account_id) {
            $monthlyCards = DB::table('monthly_cards')
                ->where('username', $userAccount->gameAccount->username ?? '')
                ->where('type', 'monthly_card')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        // Get battle pass progress (if linked to game account)
        $battlePassProgress = null;
        if ($userAccount->game_account_id) {
            $battlePassProgress = UserBattlePass::where('account_id', $userAccount->game_account_id)
                ->with('season')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return view('user.dashboard', compact(
            'userAccount',
            'stats',
            'recentPayments',
            'monthlyCards',
            'battlePassProgress'
        ));
    }

    private function getUserStats($userAccount)
    {
        $userId = $userAccount->id;
        $gameAccountId = $userAccount->game_account_id;

        $stats = [
            // Coin balance
            'web_coins' => $userAccount->coinBalance->web_coins ?? 0,
            'total_recharged' => $userAccount->coinBalance->total_recharged ?? 0,

            // Payment statistics
            'total_payments' => UserPaymentRequest::where('user_id', $userId)->count(),
            'completed_payments' => UserPaymentRequest::where('user_id', $userId)
                ->where('status', 'completed')->count(),
            'pending_payments' => UserPaymentRequest::where('user_id', $userId)
                ->where('status', 'pending')->count(),

            // Monthly cards
            'active_monthly_cards' => 0,
            'total_monthly_cards' => 0,

            // Battle pass
            'battle_pass_level' => 0,
            'battle_pass_premium' => false,
        ];

        // Get monthly cards stats if game account is linked
        if ($gameAccountId) {
            $gameAccount = DB::table('game_accounts')->where('id', $gameAccountId)->first();
            if ($gameAccount) {
                $stats['active_monthly_cards'] = DB::table('monthly_cards')
                    ->where('username', $gameAccount->username)
                    ->where('type', 'monthly_card')
                    ->where('status', 'active')
                    ->count();

                $stats['total_monthly_cards'] = DB::table('monthly_cards')
                    ->where('username', $gameAccount->username)
                    ->where('type', 'monthly_card')
                    ->count();
            }

            // Get battle pass stats
            $battlePass = UserBattlePass::where('account_id', $gameAccountId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($battlePass) {
                $stats['battle_pass_level'] = $battlePass->current_level;
                $stats['battle_pass_premium'] = $battlePass->has_premium;
            }
        }

        return $stats;
    }

    public function getQuickStats(Request $request)
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        return response()->json([
            'success' => true,
            'stats' => [
                'web_coins' => $userAccount->coinBalance->web_coins ?? 0,
                'pending_payments' => UserPaymentRequest::where('user_id', $user['id'])
                    ->where('status', 'pending')->count(),
            ]
        ]);
    }
}
