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
        $typeFilter = $request->get('type', 'all');

        // Base query for recharge logs
        $query = DB::table('recharge_logs as r')
            ->leftJoin('game_accounts as a', 'r.username', '=', 'a.username')
            ->select([
                'r.*',
                'a.id as account_id',
                'a.email',
                'a.current_balance'
            ]);

        // Apply search filters
        if ($search) {
            switch ($searchType) {
                case 'username':
                    $query->where('r.username', 'like', "%{$search}%");
                    break;
                case 'character_name':
                    $query->where('r.character_name', 'like', "%{$search}%");
                    break;
                case 'transaction_id':
                    $query->where('r.transaction_id', 'like', "%{$search}%");
                    break;
            }
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('r.status', $statusFilter);
        }

        // Apply type filter
        if ($typeFilter !== 'all') {
            $query->where('r.type', $typeFilter);
        }

        $recharges = $query->orderBy('r.created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'today_total' => DB::table('recharge_logs')
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('amount'),
            'today_count' => DB::table('recharge_logs')
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->count(),
            'month_total' => DB::table('recharge_logs')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_count' => DB::table('recharge_logs')
                ->where('status', 'pending')
                ->count()
        ];

        return view('admin.coin-recharge.index', compact('admin', 'recharges', 'search', 'searchType', 'statusFilter', 'typeFilter', 'stats'));
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
            'amount' => 'required|numeric|min:1000|max:100000000',
            'coins_added' => 'required|integer|min:1|max:1000000',
            'character_name' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        // Check if account exists
        $account = DB::table('game_accounts')->where('username', $request->username)->first();
        if (!$account) {
            return redirect()->back()->withErrors(['username' => 'Không tìm thấy tài khoản với username này.']);
        }

        // Generate transaction ID
        $transactionId = 'MANUAL_' . time() . '_' . rand(1000, 9999);

        // Create recharge log
        $rechargeId = DB::table('recharge_logs')->insertGetId([
            'username' => $request->username,
            'character_name' => $request->character_name,
            'amount' => $request->amount,
            'coins_added' => $request->coins_added,
            'type' => 'manual',
            'status' => 'completed',
            'note' => $request->note ?: 'Nạp coin thủ công bởi admin',
            'transaction_id' => $transactionId,
            'admin_id' => $admin['id'],
            'admin_ip' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update account balance
        $oldBalance = $account->current_balance;
        $newBalance = $oldBalance + $request->coins_added;
        
        DB::table('game_accounts')
            ->where('id', $account->id)
            ->update([
                'current_balance' => $newBalance,
                'total_recharge' => DB::raw('total_recharge + ' . $request->amount),
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'manual_recharge',
            'recharge',
            $rechargeId,
            $request->username,
            ['balance' => $oldBalance],
            ['balance' => $newBalance, 'amount' => $request->amount, 'coins' => $request->coins_added],
            $request->note ?: 'Nạp coin thủ công',
            $request->ip()
        );

        return redirect()->route('admin.coin-recharge.index')
            ->with('success', "Đã nạp thành công {$request->coins_added} coin cho tài khoản {$request->username}. Mã giao dịch: {$transactionId}");
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');
        
        $recharge = DB::table('recharge_logs as r')
            ->leftJoin('game_accounts as a', 'r.username', '=', 'a.username')
            ->leftJoin('admin_users as admin', 'r.admin_id', '=', 'admin.id')
            ->select([
                'r.*',
                'a.id as account_id',
                'a.email',
                'a.current_balance',
                'a.total_recharge',
                'a.vip_level',
                'admin.username as admin_username'
            ])
            ->where('r.id', $id)
            ->first();

        if (!$recharge) {
            return redirect()->route('admin.coin-recharge.index')->withErrors(['error' => 'Không tìm thấy giao dịch.']);
        }

        return view('admin.coin-recharge.show', compact('admin', 'recharge'));
    }

    public function searchAccount(Request $request)
    {
        $username = $request->get('username');
        
        if (!$username) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập username']);
        }

        $account = DB::table('game_accounts')
            ->select(['id', 'username', 'email', 'current_balance', 'total_recharge', 'vip_level', 'status'])
            ->where('username', 'like', "%{$username}%")
            ->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản']);
        }

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    public function getStatistics(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year

        $query = DB::table('recharge_logs')->where('status', 'completed');

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
            'total_amount' => $query->sum('amount'),
            'total_coins' => $query->sum('coins_added'),
            'total_transactions' => $query->count(),
            'avg_amount' => $query->avg('amount'),
        ];

        // Get top recharge users
        $topUsers = DB::table('recharge_logs')
            ->select('username', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
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
