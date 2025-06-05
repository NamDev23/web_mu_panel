<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class WithdrawController extends Controller
{
    public function index()
    {
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        if (!$user) {
            return redirect('/user/login')->withErrors(['login' => 'Tài khoản không tồn tại.']);
        }

        // Get user's coin balances from user_coins table
        $userCoins = DB::table('user_coins')->where('account_id', $user->ID)->first();
        if (!$userCoins) {
            // Create initial coin balance record
            DB::table('user_coins')->insert([
                'account_id' => $user->ID,
                'username' => $user->UserName,
                'coins' => 0,
                'total_recharged' => 0,
                'total_spent' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $userCoins = (object) [
                'coins' => 0,
                'total_recharged' => 0,
                'total_spent' => 0
            ];
        }

        // Get game coins and characters
        $gameMoney = $user->getGameMoney();
        $gameCharacters = $user->getGameCharacters();

        // Get recent withdraw transactions from coin_spend_logs
        $recentWithdraws = DB::table('coin_spend_logs')
            ->where('account_id', $user->ID)
            ->where('item_type', 'withdraw')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get withdraw statistics
        $stats = $this->getWithdrawStats($user->ID);

        return view('user.withdraw.index', compact(
            'user',
            'userCoins',
            'gameMoney',
            'gameCharacters',
            'recentWithdraws',
            'stats'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000|max:1000000',
            'character_id' => 'required|integer',
        ], [
            'amount.required' => 'Vui lòng nhập số coin muốn rút',
            'amount.integer' => 'Số coin phải là số nguyên',
            'amount.min' => 'Số coin tối thiểu là 1,000',
            'amount.max' => 'Số coin tối đa là 1,000,000',
            'character_id.required' => 'Vui lòng chọn nhân vật',
            'character_id.integer' => 'ID nhân vật không hợp lệ',
        ]);

        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);
        $amount = $request->amount;
        $characterId = $request->character_id;

        // Get user coins balance
        $userCoins = DB::table('user_coins')->where('account_id', $user->ID)->first();
        if (!$userCoins || $userCoins->coins < $amount) {
            return back()->withErrors(['error' => "Không đủ coin. Bạn có " . number_format($userCoins->coins ?? 0) . " coin."]);
        }

        // Check if character belongs to user
        $character = $user->getGameCharacters()->where('rid', $characterId)->first();
        if (!$character) {
            return back()->withErrors(['error' => 'Nhân vật không tồn tại hoặc không thuộc về bạn.']);
        }

        // Check daily withdraw limit (500,000 coins per day)
        $dailyLimit = 500000;
        $todayWithdraws = DB::table('coin_spend_logs')
            ->where('account_id', $user->ID)
            ->where('item_type', 'withdraw')
            ->whereDate('created_at', today())
            ->sum('coins_spent');

        if (($todayWithdraws + $amount) > $dailyLimit) {
            return back()->withErrors(['error' => "Vượt quá giới hạn rút coin hàng ngày (" . number_format($dailyLimit) . " coin). Hôm nay bạn đã rút " . number_format($todayWithdraws) . " coin."]);
        }

        try {
            DB::beginTransaction();

            // Get current balances
            $coinBalanceBefore = $userCoins->coins;
            $gameMoney = $user->getGameMoney();
            $gameBalanceBefore = $gameMoney ? $gameMoney->money : 0;

            // Deduct coins from user_coins balance
            DB::table('user_coins')
                ->where('account_id', $user->ID)
                ->update([
                    'coins' => DB::raw('coins - ' . $amount),
                    'total_spent' => DB::raw('total_spent + ' . $amount),
                    'updated_at' => now()
                ]);

            // Add coins to game account (as money, not realmoney)
            if ($gameMoney) {
                DB::connection('game_mysql')
                    ->table('t_money')
                    ->where('userid', $user->getGameUserId())
                    ->update([
                        'money' => DB::raw('money + ' . $amount)
                    ]);
            } else {
                // Create money record if not exists
                DB::connection('game_mysql')->table('t_money')->insert([
                    'userid' => $user->getGameUserId(),
                    'money' => $amount,
                    'realmoney' => 0,
                    'giftid' => 0,
                    'giftjifen' => 0,
                    'points' => 0,
                    'specjifen' => 0
                ]);
            }

            // Get updated balances
            $coinBalanceAfter = $coinBalanceBefore - $amount;
            $gameBalanceAfter = $gameBalanceBefore + $amount;

            // Generate transaction ID
            $transactionId = 'WITHDRAW_' . time() . '_' . rand(1000, 9999);

            // Create transaction record in coin_spend_logs
            $spendId = DB::table('coin_spend_logs')->insertGetId([
                'account_id' => $user->ID,
                'username' => $user->UserName,
                'transaction_id' => $transactionId,
                'coins_spent' => $amount,
                'item_type' => 'withdraw',
                'item_name' => "Rút coin vào game - Nhân vật {$character->rname}",
                'item_data' => json_encode([
                    'character_id' => $characterId,
                    'character_name' => $character->rname,
                    'game_userid' => $user->getGameUserId(),
                    'coin_balance_before' => $coinBalanceBefore,
                    'coin_balance_after' => $coinBalanceAfter,
                    'game_balance_before' => $gameBalanceBefore,
                    'game_balance_after' => $gameBalanceAfter
                ]),
                'description' => "Rút {$amount} coin vào nhân vật {$character->rname} trong game",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return back()->with(
                'success',
                "Rút " . number_format($amount) . " coin thành công! " .
                    "Coin đã được chuyển vào nhân vật '{$character->rname}'. " .
                    "Số dư coin hiện tại: " . number_format($coinBalanceAfter) . " coin."
            );
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function history(Request $request)
    {
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        $query = DB::table('coin_spend_logs')
            ->where('account_id', $user->ID)
            ->where('item_type', 'withdraw');

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $withdraws = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('user.withdraw.history', compact('withdraws'));
    }

    public function show($id)
    {
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        $withdraw = DB::table('coin_spend_logs')
            ->where('account_id', $user->ID)
            ->where('item_type', 'withdraw')
            ->where('id', $id)
            ->first();

        if (!$withdraw) {
            abort(404);
        }

        return view('user.withdraw.show', compact('withdraw'));
    }

    private function getWithdrawStats($userId)
    {
        $totalWithdraws = DB::table('coin_spend_logs')
            ->where('account_id', $userId)
            ->where('item_type', 'withdraw')
            ->count();

        $totalAmount = DB::table('coin_spend_logs')
            ->where('account_id', $userId)
            ->where('item_type', 'withdraw')
            ->sum('coins_spent');

        $todayAmount = DB::table('coin_spend_logs')
            ->where('account_id', $userId)
            ->where('item_type', 'withdraw')
            ->whereDate('created_at', today())
            ->sum('coins_spent');

        $dailyLimit = 500000;

        return [
            'total_withdraws' => $totalWithdraws,
            'completed_withdraws' => $totalWithdraws, // All withdraws are completed immediately
            'total_amount' => $totalAmount,
            'today_amount' => $todayAmount,
            'daily_limit' => $dailyLimit,
            'remaining_today' => max(0, $dailyLimit - $todayAmount)
        ];
    }
}
