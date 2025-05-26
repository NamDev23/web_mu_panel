<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{
    // Constructor removed - authentication handled by middleware

    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $searchType = $request->get('search_type', 'username');

        // Query real database
        $query = DB::table('game_accounts');

        if ($search) {
            switch ($searchType) {
                case 'email':
                    $query->where('email', 'like', "%{$search}%");
                    break;
                case 'phone':
                    $query->where('phone', 'like', "%{$search}%");
                    break;
                case 'full_name':
                    $query->where('full_name', 'like', "%{$search}%");
                    break;
                case 'username':
                default:
                    $query->where('username', 'like', "%{$search}%");
                    break;
            }
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.accounts.index', compact('admin', 'accounts', 'search', 'searchType'));
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');
        $account = DB::table('game_accounts')->where('id', $id)->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Get recent login logs from ip_logs table
        $recentLogins = DB::table('ip_logs')
            ->where('username', $account->username)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.accounts.show', compact('admin', 'account', 'recentLogins'));
    }

    public function ban(Request $request, $id)
    {
        $admin = Session::get('admin_user');
        $reason = $request->input('reason', 'Vi phạm quy định');

        // Get account info before ban
        $account = DB::table('game_accounts')->where('id', $id)->first();
        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Update account status
        DB::table('game_accounts')
            ->where('id', $id)
            ->update([
                'status' => 'banned',
                'ban_reason' => $reason,
                'banned_at' => now(),
                'banned_by' => $admin['id'],
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'ban_account',
            'account',
            $id,
            $account->username,
            ['status' => $account->status],
            ['status' => 'banned', 'ban_reason' => $reason],
            $reason,
            $request->ip()
        );

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã khóa tài khoản {$account->username}. Lý do: {$reason}");
    }

    public function unban($id)
    {
        $admin = Session::get('admin_user');

        // Get account info before unban
        $account = DB::table('game_accounts')->where('id', $id)->first();
        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Update account status
        DB::table('game_accounts')
            ->where('id', $id)
            ->update([
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'banned_by' => null,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'unban_account',
            'account',
            $id,
            $account->username,
            ['status' => $account->status, 'ban_reason' => $account->ban_reason],
            ['status' => 'active', 'ban_reason' => null],
            'Mở khóa tài khoản',
            request()->ip()
        );

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã mở khóa tài khoản {$account->username} thành công.");
    }

    public function edit($id)
    {
        $admin = Session::get('admin_user');
        $account = DB::table('game_accounts')->where('id', $id)->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        return view('admin.accounts.edit', compact('admin', 'account'));
    }

    public function update(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'full_name' => 'nullable|string|max:255',
            'vip_level' => 'required|integer|min:0|max:10',
            'current_balance' => 'required|numeric|min:0',
        ]);

        // Get account info before update
        $account = DB::table('game_accounts')->where('id', $id)->first();
        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        $oldData = [
            'email' => $account->email,
            'phone' => $account->phone,
            'full_name' => $account->full_name,
            'vip_level' => $account->vip_level,
            'current_balance' => $account->current_balance,
        ];

        $newData = [
            'email' => $request->email,
            'phone' => $request->phone,
            'full_name' => $request->full_name,
            'vip_level' => $request->vip_level,
            'current_balance' => $request->current_balance,
        ];

        // Update account
        DB::table('game_accounts')
            ->where('id', $id)
            ->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'full_name' => $request->full_name,
                'vip_level' => $request->vip_level,
                'current_balance' => $request->current_balance,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction($admin, 'edit_account', 'account', $id, $account->username,
            $oldData, $newData, 'Cập nhật thông tin tài khoản', $request->ip());

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã cập nhật thông tin tài khoản {$account->username} thành công.");
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
