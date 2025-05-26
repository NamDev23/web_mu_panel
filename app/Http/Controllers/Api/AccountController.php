<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Account;
use App\Character;
use DB;

class AccountController extends Controller
{
    /**
     * Search accounts
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');
            $server = $request->get('server');

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập từ khóa tìm kiếm'
                ]);
            }

            $accountQuery = Account::query();

            // Search by multiple fields
            $accountQuery->where(function ($q) use ($query) {
                $q->where('UserName', 'like', "%{$query}%")
                    ->orWhere('Email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('UserID', 'like', "%{$query}%");
            });

            // Add character count
            $accountQuery->withCount(['characters' => function ($q) use ($server) {
                if ($server) {
                    $q->where('serverid', $server);
                }
            }]);

            $accounts = $accountQuery->limit(50)->get();

            return response()->json([
                'success' => true,
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account details
     */
    public function show($id)
    {
        try {
            $account = Account::findOrFail($id);

            // Get characters
            $characters = Character::where('userid', $id)
                ->select('rid', 'rname', 'level', 'serverid', 'occupation', 'isdel', 'lasttime')
                ->get()
                ->map(function ($char) {
                    $char->server_name = 'Server ' . $char->serverid;
                    $char->is_banned = $char->isdel == 1;
                    return $char;
                });

            // Get recent login logs
            $loginLogs = LoginLog::where('user_id', $id)
                ->orderBy('login_at', 'desc')
                ->limit(10)
                ->get();

            // Get payment history
            $payments = Recharge::where('uid', $id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'account' => $account,
                'characters' => $characters,
                'login_logs' => $loginLogs,
                'payments' => $payments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update account
     */
    public function update(Request $request, $id)
    {
        try {
            $account = Account::findOrFail($id);

            $request->validate([
                'Email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'Money' => 'nullable|integer|min:0'
            ]);

            $account->update($request->only(['Email', 'phone', 'Money']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tài khoản thành công',
                'account' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ban account
     */
    public function ban($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->groupid = 99; // Banned group
            $account->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã ban tài khoản thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban account
     */
    public function unban($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->groupid = 0; // Normal user group
            $account->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã unban tài khoản thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add coin to account
     */
    public function addCoin(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|integer|min:1',
                'reason' => 'nullable|string|max:255'
            ]);

            $account = Account::findOrFail($id);
            $account->Money += $request->amount;
            $account->save();

            // Log transaction
            DB::table('coin_transactions')->insert([
                'user_id' => $id,
                'amount' => $request->amount,
                'type' => 'admin_add',
                'reason' => $request->reason,
                'admin_id' => auth()->id(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm coin thành công',
                'new_balance' => $account->Money
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
