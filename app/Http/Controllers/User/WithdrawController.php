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

        // Get user's coin balances
        $webCoins = $user->getWebCoins();
        if (!$webCoins) {
            // Create initial coin balance record
            DB::table('t_web_coins')->insert([
                'account_id' => $user->ID,
                'balance' => 0,
                'total_recharged' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $webCoins = (object) ['balance' => 0, 'total_recharged' => 0];
        }

        // Get game coins and characters
        $gameMoney = $user->getGameMoney();
        $gameCharacters = $user->getGameCharacters();

        // Get recent withdraw transactions
        $recentWithdraws = DB::table('t_coin_transactions')
            ->where('account_id', $user->ID)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get withdraw statistics
        $stats = $this->getWithdrawStats($user->ID);

        return view('user.withdraw.index', compact(
            'user',
            'webCoins',
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

        // Get web coins balance
        $webCoins = $user->getWebCoins();
        if (!$webCoins || $webCoins->balance < $amount) {
            return back()->withErrors(['error' => "Không đủ coin. Bạn có " . number_format($webCoins->balance ?? 0) . " coin."]);
        }

        // Check if character belongs to user
        $character = $user->getGameCharacters()->where('rid', $characterId)->first();
        if (!$character) {
            return back()->withErrors(['error' => 'Nhân vật không tồn tại hoặc không thuộc về bạn.']);
        }

        // Check daily withdraw limit (500,000 coins per day)
        $dailyLimit = 500000;
        $todayWithdraws = DB::table('t_coin_transactions')
            ->where('account_id', $user->ID)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->whereDate('created_at', today())
            ->sum('amount');

        if (($todayWithdraws + $amount) > $dailyLimit) {
            return back()->withErrors(['error' => "Vượt quá giới hạn rút coin hàng ngày (" . number_format($dailyLimit) . " coin). Hôm nay bạn đã rút " . number_format($todayWithdraws) . " coin."]);
        }

        try {
            DB::beginTransaction();

            // Get current balances
            $webBalanceBefore = $webCoins->balance;
            $gameMoney = $user->getGameMoney();
            $gameBalanceBefore = $gameMoney ? $gameMoney->money : 0;

            // Deduct coins from web balance
            DB::table('t_web_coins')
                ->where('account_id', $user->ID)
                ->decrement('balance', $amount);

            // Add coins to game account
            DB::connection('game_mysql')
                ->table('t_money')
                ->where('userid', $user->getGameUserId())
                ->increment('money', $amount);

            // Get updated balances
            $webBalanceAfter = $webBalanceBefore - $amount;
            $gameBalanceAfter = $gameBalanceBefore + $amount;

            // Create transaction record
            $transactionId = DB::table('t_coin_transactions')->insertGetId([
                'account_id' => $user->ID,
                'type' => 'spend',
                'amount' => $amount,
                'balance_before' => $webBalanceBefore,
                'balance_after' => $webBalanceAfter,
                'description' => json_encode([
                    'type' => 'withdraw_to_game',
                    'character_id' => $characterId,
                    'character_name' => $character->rname,
                    'game_userid' => $user->getGameUserId(),
                    'web_balance_before' => $webBalanceBefore,
                    'web_balance_after' => $webBalanceAfter,
                    'game_balance_before' => $gameBalanceBefore,
                    'game_balance_after' => $gameBalanceAfter,
                    'status' => 'completed'
                ]),
                'reference_type' => 'withdraw',
                'reference_id' => $characterId,
                'processed_by' => $user->ID,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return back()->with(
                'success',
                "Rút " . number_format($amount) . " coin thành công! " .
                    "Coin đã được chuyển vào nhân vật '{$character->rname}'. " .
                    "Số dư web hiện tại: " . number_format($webBalanceAfter) . " coin."
            );
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    // TODO: Implement history and show methods with new database structure
    /*
    public function history(Request $request)
    {
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        $query = DB::table('t_coin_transactions')
            ->where('account_id', $user->ID)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw');

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

        $withdraw = DB::table('t_coin_transactions')
            ->where('account_id', $user->ID)
            ->where('id', $id)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->first();

        if (!$withdraw) {
            abort(404);
        }

        return view('user.withdraw.show', compact('withdraw'));
    }
    */

    private function getWithdrawStats($userId)
    {
        $totalWithdraws = DB::table('t_coin_transactions')
            ->where('account_id', $userId)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->count();

        $totalAmount = DB::table('t_coin_transactions')
            ->where('account_id', $userId)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->sum('amount');

        $todayAmount = DB::table('t_coin_transactions')
            ->where('account_id', $userId)
            ->where('type', 'spend')
            ->where('reference_type', 'withdraw')
            ->whereDate('created_at', today())
            ->sum('amount');

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
