<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BattlePassController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');

        // Base query for battle passes only
        $query = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->select([
                'm.*',
                'a.id as account_id',
                'a.email',
                'a.full_name'
            ])
            ->where('m.type', 'battle_pass');

        // Apply search filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('m.username', 'like', "%{$search}%")
                  ->orWhere('a.email', 'like', "%{$search}%")
                  ->orWhere('a.full_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('m.status', $statusFilter);
        }

        $battlePasses = $query->orderBy('m.created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_passes' => DB::table('monthly_cards')->where('type', 'battle_pass')->count(),
            'active_passes' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', 'active')->count(),
            'expired_passes' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', 'expired')->count(),
            'total_revenue' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', '!=', 'cancelled')->sum('price'),
            'monthly_revenue' => DB::table('monthly_cards')
                ->where('type', 'battle_pass')
                ->where('status', '!=', 'cancelled')
                ->whereMonth('created_at', now()->month)
                ->sum('price'),
            'premium_users' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', 'active')->distinct('username')->count(),
        ];

        return view('admin.battle-pass.index', compact('admin', 'battlePasses', 'search', 'statusFilter', 'stats'));
    }

    public function create()
    {
        $admin = Session::get('admin_user');
        return view('admin.battle-pass.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'username' => 'required|string|max:50',
            'season_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1|max:365',
            'max_level' => 'required|integer|min:1|max:100',
            'free_rewards' => 'required|string',
            'premium_rewards' => 'required|string',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if account exists
        $account = DB::table('game_accounts')->where('username', $request->username)->first();
        if (!$account) {
            return redirect()->back()->withErrors(['username' => 'Không tìm thấy tài khoản với username này.'])->withInput();
        }

        // Check if user already has active battle pass
        $existingPass = DB::table('monthly_cards')
            ->where('username', $request->username)
            ->where('type', 'battle_pass')
            ->where('status', 'active')
            ->first();

        if ($existingPass) {
            return redirect()->back()->withErrors(['username' => 'Người chơi đã có battle pass đang hoạt động.'])->withInput();
        }

        // Calculate expiry date
        $expiresAt = now()->addDays($request->duration_days);

        // Create battle pass
        $passId = DB::table('monthly_cards')->insertGetId([
            'username' => $request->username,
            'type' => 'battle_pass',
            'package_name' => $request->season_name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'daily_rewards' => json_encode([
                'max_level' => $request->max_level,
                'free_rewards' => $request->free_rewards,
                'premium_rewards' => $request->premium_rewards
            ]),
            'bonus_rewards' => null,
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
            'create_battle_pass',
            'battle_pass',
            $passId,
            $request->username . ' - ' . $request->season_name,
            [],
            [
                'season_name' => $request->season_name,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'max_level' => $request->max_level,
                'status' => 'active'
            ],
            'Tạo battle pass cho ' . $request->username,
            $request->ip()
        );

        return redirect()->route('admin.battle-pass.index')
            ->with('success', 'Đã tạo battle pass thành công cho ' . $request->username);
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        $battlePass = DB::table('monthly_cards as m')
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
            ->where('m.type', 'battle_pass')
            ->first();

        if (!$battlePass) {
            return redirect()->route('admin.battle-pass.index')->withErrors(['error' => 'Không tìm thấy battle pass này.']);
        }

        // Get reward history
        $rewardHistory = DB::table('monthly_card_rewards')
            ->where('card_id', $id)
            ->orderBy('claimed_at', 'desc')
            ->get();

        return view('admin.battle-pass.show', compact('admin', 'battlePass', 'rewardHistory'));
    }

    public function extend(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'extend_days' => 'required|integer|min:1|max:365',
            'reason' => 'required|string|max:500'
        ]);

        $battlePass = DB::table('monthly_cards')->where('id', $id)->where('type', 'battle_pass')->first();
        if (!$battlePass) {
            return redirect()->back()->withErrors(['error' => 'Không tìm thấy battle pass này.']);
        }

        $oldExpiresAt = $battlePass->expires_at;
        $newExpiresAt = date('Y-m-d H:i:s', strtotime($battlePass->expires_at . ' +' . $request->extend_days . ' days'));

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
            'extend_battle_pass',
            'battle_pass',
            $id,
            $battlePass->username . ' - ' . $battlePass->package_name,
            ['expires_at' => $oldExpiresAt],
            ['expires_at' => $newExpiresAt],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.battle-pass.show', $id)
            ->with('success', "Đã gia hạn {$request->extend_days} ngày. Lý do: {$request->reason}");
    }

    public function cancel(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $battlePass = DB::table('monthly_cards')->where('id', $id)->where('type', 'battle_pass')->first();
        if (!$battlePass) {
            return redirect()->back()->withErrors(['error' => 'Không tìm thấy battle pass này.']);
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
            'cancel_battle_pass',
            'battle_pass',
            $id,
            $battlePass->username . ' - ' . $battlePass->package_name,
            ['status' => $battlePass->status],
            ['status' => 'cancelled', 'cancel_reason' => $request->reason],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.battle-pass.show', $id)
            ->with('success', "Đã hủy battle pass. Lý do: {$request->reason}");
    }

    public function searchAccount(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $accounts = DB::table('game_accounts')
            ->select('id', 'username', 'email', 'full_name', 'vip_level')
            ->where(function($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($accounts);
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
