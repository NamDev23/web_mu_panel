<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Account;

class AccountController extends Controller
{
    protected $gameDataService;

    public function __construct(GameDataService $gameDataService)
    {
        $this->gameDataService = $gameDataService;
    }

    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $searchType = $request->get('search_type', 'username');

        // Query real database
        $query = DB::table('t_account');

        if ($search) {
            switch ($searchType) {
                case 'email':
                    $query->where('Email', 'like', "%{$search}%");
                    break;
                case 'username':
                default:
                    $query->where('UserName', 'like', "%{$search}%");
                    break;
            }
        }

        $accounts = $query->orderBy('CreateTime', 'desc')->paginate(20);

        // Optimize: Use GameDataService for batch loading with caching
        if (!$accounts->isEmpty()) {
            $accountIds = $accounts->pluck('ID')->toArray();
            $gameData = $this->gameDataService->getAccountsGameData($accountIds);

            foreach ($accounts as $account) {
                $data = $gameData[$account->ID] ?? ['characters_count' => 0, 'total_money' => 0];
                $account->characters_count = $data['characters_count'];
                $account->total_money = $data['total_money'];
            }
        }

        return view('admin.accounts.index', compact('admin', 'accounts', 'search', 'searchType'));
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        // Get account from website database
        $account = DB::table('t_account')->where('ID', $id)->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Optimize: Use GameDataService with caching
        $gameData = $this->gameDataService->getAccountGameData($account->ID);
        $account->characters_count = $gameData['characters_count'];
        $account->total_money = $gameData['total_money'];

        // Get recent login logs (mock data for now)
        $recentLogins = [];

        return view('admin.accounts.show', compact('admin', 'account', 'recentLogins'));
    }

    public function edit($id)
    {
        $admin = Session::get('admin_user');

        // Get account from website database
        $account = DB::table('t_account')->where('ID', $id)->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Optimize: Use GameDataService with caching
        $gameData = $this->gameDataService->getAccountGameData($account->ID);
        $account->characters_count = $gameData['characters_count'];
        $account->total_money = $gameData['total_money'];

        return view('admin.accounts.edit', compact('admin', 'account'));
    }

    public function update(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        // Get account from website database
        $account = DB::table('t_account')->where('ID', $id)->first();

        if (!$account) {
            return redirect()->route('admin.accounts.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Validate input
        $rules = [
            'status' => 'required|in:0,1',
            'email' => 'nullable|email|max:255',
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $request->validate($rules);

        $oldData = (array) $account;

        // Prepare update data
        $updateData = [
            'Status' => $request->status,
            'UpdateTime' => now(),
        ];

        // Update email if provided
        if ($request->filled('email')) {
            $updateData['Email'] = $request->email;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['PassWord'] = md5($request->password); // MU Online typically uses MD5
        }

        // Update account
        DB::table('t_account')
            ->where('ID', $id)
            ->update($updateData);

        // Clear cache for this account
        $this->gameDataService->clearAccountCache($id);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'update_account',
            'account',
            $id,
            $account->UserName,
            $oldData,
            $updateData,
            'Cập nhật thông tin tài khoản',
            $request->ip()
        );

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã cập nhật tài khoản {$account->UserName} thành công.");
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

    public function ban(Request $request, $id)
    {
        $admin = Session::get('admin_user');
        $reason = $request->input('reason', 'Vi phạm quy định');

        // Get account info before ban
        $account = DB::table('t_account')->where('ID', $id)->first();
        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Update account status
        DB::table('t_account')
            ->where('ID', $id)
            ->update([
                'Status' => 0, // 0 = banned
                'UpdateTime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'ban_account',
            'account',
            $id,
            $account->UserName,
            ['Status' => $account->Status],
            ['Status' => 0, 'ban_reason' => $reason],
            $reason,
            $request->ip()
        );

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã khóa tài khoản {$account->UserName}. Lý do: {$reason}");
    }

    public function unban($id)
    {
        $admin = Session::get('admin_user');

        // Get account info before unban
        $account = DB::table('t_account')->where('ID', $id)->first();
        if (!$account) {
            return redirect()->route('admin.accounts.index')->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        // Update account status
        DB::table('t_account')
            ->where('ID', $id)
            ->update([
                'Status' => 1, // 1 = active
                'UpdateTime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'unban_account',
            'account',
            $id,
            $account->UserName,
            ['Status' => $account->Status],
            ['Status' => 1],
            'Mở khóa tài khoản',
            request()->ip()
        );

        return redirect()->route('admin.accounts.show', $id)
            ->with('success', "Đã mở khóa tài khoản {$account->UserName} thành công.");
    }
}
