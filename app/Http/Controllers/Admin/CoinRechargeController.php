<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CoinRechargeController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $searchType = $request->get('search_type', 'username');
        $statusFilter = $request->get('status', 'all');

        // Get recharge logs with account info
        $query = DB::table('coin_recharge_logs as r')
            ->leftJoin('t_account as a', 'r.account_id', '=', 'a.ID')
            ->select([
                'r.*',
                'a.UserName',
                'a.Email',
                'a.Status as account_status'
            ]);

        // Apply search filters
        if ($search) {
            switch ($searchType) {
                case 'email':
                    $query->where('a.Email', 'like', "%{$search}%");
                    break;
                case 'transaction_id':
                    $query->where('r.transaction_id', 'like', "%{$search}%");
                    break;
                case 'username':
                default:
                    $query->where('r.username', 'like', "%{$search}%");
                    break;
            }
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('r.status', $statusFilter);
        }

        $recharges = $query->orderBy('r.created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'today_total' => DB::table('coin_recharge_logs')
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('amount_vnd'),
            'today_count' => DB::table('coin_recharge_logs')
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->count(),
            'month_total' => DB::table('coin_recharge_logs')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'completed')
                ->sum('amount_vnd'),
            'pending_count' => DB::table('coin_recharge_logs')
                ->where('status', 'pending')
                ->count()
        ];

        return view('admin.coin-recharge.index', compact('admin', 'recharges', 'search', 'searchType', 'statusFilter', 'stats'));
    }

    public function create()
    {
        $admin = Session::get('admin_user');
        return view('admin.coin-recharge.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'username' => 'required|string|max:50',
            'amount_vnd' => 'required|numeric|min:1000|max:100000000',
            'coins_added' => 'required|integer|min:1|max:1000000',
            'note' => 'required|string|max:500',
        ]);

        // Check if account exists
        $account = DB::table('t_account')->where('UserName', $request->username)->first();
        if (!$account) {
            return redirect()->back()->withErrors(['username' => 'Không tìm thấy tài khoản với username này.']);
        }

        // Generate transaction ID
        $transactionId = 'MANUAL_' . time() . '_' . rand(1000, 9999);

        // Get or create user coins record
        $userCoins = DB::table('user_coins')->where('account_id', $account->ID)->first();
        $oldCoins = $userCoins ? $userCoins->coins : 0;
        $newCoins = $oldCoins + $request->coins_added;

        // Update or create user coins
        if ($userCoins) {
            DB::table('user_coins')
                ->where('account_id', $account->ID)
                ->update([
                    'coins' => $newCoins,
                    'total_recharged' => DB::raw('total_recharged + ' . $request->amount_vnd),
                    'updated_at' => now()
                ]);
        } else {
            DB::table('user_coins')->insert([
                'account_id' => $account->ID,
                'username' => $account->UserName,
                'coins' => $newCoins,
                'total_recharged' => $request->amount_vnd,
                'total_spent' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Create recharge log
        $rechargeId = DB::table('coin_recharge_logs')->insertGetId([
            'account_id' => $account->ID,
            'username' => $account->UserName,
            'transaction_id' => $transactionId,
            'amount_vnd' => $request->amount_vnd,
            'coins_added' => $request->coins_added,
            'type' => 'manual',
            'status' => 'completed',
            'note' => $request->note,
            'admin_id' => $admin['id'],
            'admin_username' => $admin['username'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'manual_coin_recharge',
            'coin_recharge',
            $rechargeId,
            $account->UserName,
            ['coins' => $oldCoins],
            ['coins' => $newCoins, 'amount_vnd' => $request->amount_vnd, 'coins_added' => $request->coins_added],
            $request->note,
            $request->ip()
        );

        return redirect()->route('admin.coin-recharge.index')
            ->with('success', "Đã nạp thành công {$request->coins_added} coin cho tài khoản {$request->username}. Mã giao dịch: {$transactionId}");
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        // Get recharge log with account info
        $recharge = DB::table('coin_recharge_logs as r')
            ->leftJoin('t_account as a', 'r.account_id', '=', 'a.ID')
            ->leftJoin('admin_users as admin', 'r.admin_id', '=', 'admin.id')
            ->select([
                'r.*',
                'a.UserName',
                'a.Email',
                'a.Status as account_status',
                'admin.username as admin_username'
            ])
            ->where('r.id', $id)
            ->first();

        if (!$recharge) {
            return redirect()->route('admin.coin-recharge.index')->withErrors(['error' => 'Không tìm thấy giao dịch.']);
        }

        // Get current user coins
        $userCoins = DB::table('user_coins')->where('account_id', $recharge->account_id)->first();
        
        // Get account info for the view
        $account = DB::table('t_account')->where('ID', $recharge->account_id)->first();
        
        // Get money info
        $gameUserId = 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT);
        $money = DB::connection('game_mysql')
            ->table('t_money')
            ->where('userid', $gameUserId)
            ->first();

        // Create default money object if not exists
        if (!$money) {
            $money = (object) [
                'userid' => $gameUserId,
                'YuanBao' => 0,
                'Money' => 0,
                'CreateTime' => null,
                'UpdateTime' => null
            ];
        }

        return view('admin.coin-recharge.show', compact('admin', 'recharge', 'userCoins', 'account', 'money'));
    }

    public function searchAccount(Request $request)
    {
        $username = $request->get('username');

        if (!$username) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập username']);
        }

        $account = DB::table('t_account')
            ->select(['ID', 'UserName', 'Email', 'Status'])
            ->where('UserName', 'like', "%{$username}%")
            ->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản']);
        }

        // Get user coins info
        $userCoins = DB::table('user_coins')->where('account_id', $account->ID)->first();
        $account->current_coins = $userCoins ? $userCoins->coins : 0;
        $account->total_recharged = $userCoins ? $userCoins->total_recharged : 0;
        $account->total_spent = $userCoins ? $userCoins->total_spent : 0;

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    public function getStatistics(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year

        $query = DB::table('coin_recharge_logs')->where('status', 'completed');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $stats = [
            'total_amount' => $query->sum('amount_vnd'),
            'total_coins' => $query->sum('coins_added'),
            'total_transactions' => $query->count(),
            'avg_amount' => $query->avg('amount_vnd'),
        ];

        // Get top recharge users
        $topUsers = DB::table('coin_recharge_logs')
            ->select('username', DB::raw('SUM(amount_vnd) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->where('status', 'completed')
            ->groupBy('username')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'top_users' => $topUsers
        ]);
    }

    private function logAdminAction($admin, $action, $targetType, $targetId, $targetName, $oldData, $newData, $reason, $ip)
    {
        DB::table('admin_action_logs')->insert([
            'admin_id' => $admin['id'],
            'admin_username' => $admin['username'],
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_name' => $targetName,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'reason' => $reason,
            'ip_address' => $ip,
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
