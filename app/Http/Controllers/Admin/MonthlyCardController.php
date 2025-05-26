<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MonthlyCardController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');
        $typeFilter = $request->get('type', 'all');

        // Base query for monthly cards only
        $query = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->select([
                'm.*',
                'a.id as account_id',
                'a.email',
                'a.full_name'
            ])
            ->where('m.type', 'monthly_card');

        // Apply search filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('m.username', 'like', "%{$search}%")
                    ->orWhere('a.email', 'like', "%{$search}%")
                    ->orWhere('a.full_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('m.status', $statusFilter);
        }

        // No type filter needed - only monthly cards

        $monthlyCards = $query->orderBy('m.created_at', 'desc')->paginate(20);

        // Get statistics for monthly cards only
        $stats = [
            'total_cards' => DB::table('monthly_cards')->where('type', 'monthly_card')->count(),
            'active_cards' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', 'active')->count(),
            'expired_cards' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', 'expired')->count(),
            'cancelled_cards' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', 'cancelled')->count(),
            'total_revenue' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', '!=', 'cancelled')->sum('price'),
            'monthly_revenue' => DB::table('monthly_cards')
                ->where('type', 'monthly_card')
                ->where('status', '!=', 'cancelled')
                ->whereMonth('created_at', now()->month)
                ->sum('price'),
        ];

        return view('admin.monthly-cards.index', compact('admin', 'monthlyCards', 'search', 'statusFilter', 'stats'));
    }

    public function create()
    {
        $admin = Session::get('admin_user');
        return view('admin.monthly-cards.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'username' => 'required|string|max:50',
            'package_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1|max:365',
            'daily_coins' => 'required|integer|min:1',
            'bonus_coins' => 'nullable|integer|min:0',
            'daily_items' => 'nullable|string',
            'bonus_items' => 'nullable|string',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if account exists
        $account = DB::table('game_accounts')->where('username', $request->username)->first();
        if (!$account) {
            return redirect()->back()->withErrors(['username' => 'Không tìm thấy tài khoản với username này.'])->withInput();
        }

        // Check if user already has active monthly card
        $existingCard = DB::table('monthly_cards')
            ->where('username', $request->username)
            ->where('type', 'monthly_card')
            ->where('status', 'active')
            ->first();

        if ($existingCard) {
            return redirect()->back()->withErrors(['username' => 'Người chơi đã có thẻ tháng đang hoạt động.'])->withInput();
        }

        // Calculate expiry date
        $expiresAt = now()->addDays($request->duration_days);

        // Create monthly card
        $cardId = DB::table('monthly_cards')->insertGetId([
            'username' => $request->username,
            'type' => 'monthly_card',
            'package_name' => $request->package_name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'daily_rewards' => json_encode([
                'daily_coins' => $request->daily_coins,
                'bonus_coins' => $request->bonus_coins,
                'daily_items' => $request->daily_items,
            ]),
            'bonus_rewards' => $request->bonus_items,
            'description' => $request->description,
            'status' => 'active',
            'purchased_at' => now(),
            'expires_at' => $expiresAt,
            'created_by' => $admin['id'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'create_monthly_card',
            'monthly_card',
            $cardId,
            $request->username . ' - ' . $request->package_name,
            [],
            [
                'type' => 'monthly_card',
                'package_name' => $request->package_name,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'daily_coins' => $request->daily_coins,
                'status' => 'active'
            ],
            'Tạo thẻ tháng cho ' . $request->username,
            $request->ip()
        );

        return redirect()->route('admin.monthly-cards.index')
            ->with('success', 'Đã tạo thẻ tháng thành công cho ' . $request->username);
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        $card = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->leftJoin('admin_users as admin', 'm.created_by', '=', 'admin.id')
            ->select([
                'm.*',
                'a.id as account_id',
                'a.email',
                'a.full_name',
                'a.vip_level',
                'admin.username as created_by_username'
            ])
            ->where('m.id', $id)
            ->where('m.type', 'monthly_card')
            ->first();

        if (!$card) {
            return redirect()->route('admin.monthly-cards.index')->withErrors(['error' => 'Không tìm thấy thẻ này.']);
        }

        // Get reward history
        $rewardHistory = DB::table('monthly_card_rewards')
            ->where('card_id', $id)
            ->orderBy('claimed_at', 'desc')
            ->get();

        return view('admin.monthly-cards.show', compact('admin', 'card', 'rewardHistory'));
    }

    public function extend(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'extend_days' => 'required|integer|min:1|max:365',
            'reason' => 'required|string|max:500'
        ]);

        $card = DB::table('monthly_cards')->where('id', $id)->where('type', 'monthly_card')->first();
        if (!$card) {
            return redirect()->back()->withErrors(['error' => 'Không tìm thấy thẻ này.']);
        }

        $oldExpiresAt = $card->expires_at;
        $newExpiresAt = date('Y-m-d H:i:s', strtotime($card->expires_at . ' +' . $request->extend_days . ' days'));

        // Update expiry date
        DB::table('monthly_cards')
            ->where('id', $id)
            ->update([
                'expires_at' => $newExpiresAt,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'extend_' . $card->type,
            $card->type,
            $id,
            $card->username . ' - ' . $card->package_name,
            ['expires_at' => $oldExpiresAt],
            ['expires_at' => $newExpiresAt],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.monthly-cards.show', $id)
            ->with('success', "Đã gia hạn {$request->extend_days} ngày. Lý do: {$request->reason}");
    }

    public function cancel(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $card = DB::table('monthly_cards')->where('id', $id)->where('type', 'monthly_card')->first();
        if (!$card) {
            return redirect()->back()->withErrors(['error' => 'Không tìm thấy thẻ này.']);
        }

        // Update status to cancelled
        DB::table('monthly_cards')
            ->where('id', $id)
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $admin['id'],
                'cancel_reason' => $request->reason,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'cancel_' . $card->type,
            $card->type,
            $id,
            $card->username . ' - ' . $card->package_name,
            ['status' => $card->status],
            ['status' => 'cancelled', 'cancel_reason' => $request->reason],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.monthly-cards.show', $id)
            ->with('success', "Đã hủy thẻ. Lý do: {$request->reason}");
    }

    public function searchAccount(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $accounts = DB::table('game_accounts')
            ->select('id', 'username', 'email', 'full_name', 'vip_level')
            ->where(function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($accounts);
    }

    public function statistics(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        $endDate = now();

        // Cards by day
        $cardsByDay = DB::table('monthly_cards')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), 'type')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'), 'type')
            ->orderBy('date')
            ->get();

        // Revenue by day
        $revenueByDay = DB::table('monthly_cards')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(price) as revenue'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Popular packages
        $popularPackages = DB::table('monthly_cards')
            ->select('package_name', 'type', DB::raw('COUNT(*) as count'), DB::raw('SUM(price) as revenue'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('package_name', 'type')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'cards_by_day' => $cardsByDay,
            'revenue_by_day' => $revenueByDay,
            'popular_packages' => $popularPackages
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
