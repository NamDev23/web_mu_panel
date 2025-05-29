<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\GameAccount;
use App\Models\UserTransactionLog;
use App\Models\WithdrawRequest;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with(['coinBalance', 'gameAccount'])->find($user['id']);

        // Get recent withdraw requests
        $recentWithdraws = WithdrawRequest::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get withdraw statistics
        $stats = $this->getWithdrawStats($user['id']);

        return view('user.withdraw.index', compact(
            'userAccount',
            'recentWithdraws',
            'stats'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000|max:1000000',
            'game_username' => 'required|string|min:3|max:50',
        ], [
            'amount.required' => 'Vui lòng nhập số coin muốn rút',
            'amount.integer' => 'Số coin phải là số nguyên',
            'amount.min' => 'Số coin tối thiểu là 1,000',
            'amount.max' => 'Số coin tối đa là 1,000,000',
            'game_username.required' => 'Vui lòng nhập tên tài khoản game',
            'game_username.min' => 'Tên tài khoản game phải có ít nhất 3 ký tự',
            'game_username.max' => 'Tên tài khoản game không được quá 50 ký tự',
        ]);

        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        $amount = $request->amount;
        $gameUsername = $request->game_username;

        // Check if user has enough coins
        if (!$userAccount->hasEnoughCoins($amount)) {
            return back()->withErrors(['error' => "Không đủ coin. Bạn có {$userAccount->getCurrentCoins()} coin."]);
        }

        // Check if game account exists
        $gameAccount = GameAccount::where('username', $gameUsername)->first();
        if (!$gameAccount) {
            return back()->withErrors(['error' => 'Không tìm thấy tài khoản game với tên này.']);
        }

        // Check if game account is active
        if (!$gameAccount->isActive()) {
            return back()->withErrors(['error' => 'Tài khoản game không hoạt động hoặc bị khóa.']);
        }

        // Check daily withdraw limit (example: 500,000 coins per day)
        $dailyLimit = 500000;
        $todayWithdraws = WithdrawRequest::where('user_id', $user['id'])
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        if (($todayWithdraws + $amount) > $dailyLimit) {
            return back()->withErrors(['error' => "Vượt quá giới hạn rút coin hàng ngày ({$dailyLimit} coin). Hôm nay bạn đã rút {$todayWithdraws} coin."]);
        }

        try {
            DB::beginTransaction();

            // Create withdraw request
            $withdrawRequest = WithdrawRequest::create([
                'user_id' => $user['id'],
                'game_account_id' => $gameAccount->id,
                'game_username' => $gameUsername,
                'amount' => $amount,
                'status' => 'processing',
                'web_coins_before' => $userAccount->getCurrentCoins(),
                'game_coins_before' => $gameAccount->current_balance,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Deduct coins from web balance
            $userAccount->deductCoins(
                $amount,
                "Rút coin sang tài khoản game: {$gameUsername}",
                $withdrawRequest
            );

            // Add coins to game account
            $gameAccount->addCoins(
                $amount,
                "Nhận coin từ web account: {$userAccount->username}"
            );

            // Update withdraw request with after balances
            $withdrawRequest->update([
                'web_coins_after' => $userAccount->getCurrentCoins(),
                'game_coins_after' => $gameAccount->current_balance,
                'status' => 'completed',
                'processed_at' => now()
            ]);

            // Log transfer transaction
            UserTransactionLog::create([
                'user_id' => $user['id'],
                'type' => 'transfer_to_game',
                'description' => "Rút {$amount} coin sang tài khoản game: {$gameUsername}",
                'coin_amount' => -$amount,
                'coin_before' => $withdrawRequest->web_coins_before,
                'coin_after' => $withdrawRequest->web_coins_after,
                'metadata' => [
                    'withdraw_request_id' => $withdrawRequest->id,
                    'game_username' => $gameUsername,
                    'game_account_id' => $gameAccount->id,
                    'game_coins_before' => $withdrawRequest->game_coins_before,
                    'game_coins_after' => $withdrawRequest->game_coins_after
                ],
                'reference_type' => WithdrawRequest::class,
                'reference_id' => $withdrawRequest->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return back()->with('success', 
                "Rút {$amount} coin thành công! " .
                "Coin đã được chuyển vào tài khoản game '{$gameUsername}'. " .
                "Số dư web hiện tại: " . number_format($userAccount->getCurrentCoins()) . " coin."
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function history(Request $request)
    {
        $user = Session::get('user_account');
        
        $query = WithdrawRequest::where('user_id', $user['id']);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

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
        $user = Session::get('user_account');
        
        $withdraw = WithdrawRequest::where('user_id', $user['id'])
            ->where('id', $id)
            ->firstOrFail();

        return view('user.withdraw.show', compact('withdraw'));
    }

    private function getWithdrawStats($userId)
    {
        return [
            'total_withdraws' => WithdrawRequest::where('user_id', $userId)->count(),
            'completed_withdraws' => WithdrawRequest::where('user_id', $userId)
                ->where('status', 'completed')->count(),
            'total_amount' => WithdrawRequest::where('user_id', $userId)
                ->where('status', 'completed')->sum('amount'),
            'today_amount' => WithdrawRequest::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'daily_limit' => 500000,
            'remaining_today' => max(0, 500000 - WithdrawRequest::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'))
        ];
    }
}
