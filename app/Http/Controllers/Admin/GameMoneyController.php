<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GameMoneyController extends Controller
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

        // Get accounts with money data
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

        // Get money data for each account
        foreach ($accounts as $account) {
            $gameUserId = 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT);
            
            try {
                $money = DB::connection('game_mysql')
                    ->table('t_money')
                    ->where('userid', $gameUserId)
                    ->first();
                
                $account->yuanbao = $money ? ($money->realmoney ?? 0) : 0;
                $account->money = $money ? ($money->money ?? 0) : 0;
            } catch (\Exception $e) {
                $account->yuanbao = 0;
                $account->money = 0;
            }
        }

        return view('admin.game-money.index', compact('admin', 'accounts', 'search', 'searchType'));
    }

    public function show($accountId)
    {
        $admin = Session::get('admin_user');
        
        // Get account info
        $account = DB::table('t_account')->where('ID', $accountId)->first();
        if (!$account) {
            return redirect()->route('admin.game-money.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        $gameUserId = 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT);
        
        // Get money details
        $money = DB::connection('game_mysql')
            ->table('t_money')
            ->where('userid', $gameUserId)
            ->first();

        if (!$money) {
            // Create money record if not exists
            DB::connection('game_mysql')->table('t_money')->insert([
                'userid' => $gameUserId,
                'money' => 0,
                'realmoney' => 0,
                'giftid' => 0,
                'giftjifen' => 0,
                'points' => 0,
                'specjifen' => 0
            ]);

            $money = (object) [
                'userid' => $gameUserId,
                'money' => 0,
                'realmoney' => 0,
                'giftid' => 0,
                'giftjifen' => 0,
                'points' => 0,
                'specjifen' => 0
            ];
        }

        return view('admin.game-money.show', compact('admin', 'account', 'money'));
    }

    public function edit($accountId)
    {
        $admin = Session::get('admin_user');
        
        // Get account info
        $account = DB::table('t_account')->where('ID', $accountId)->first();
        if (!$account) {
            return redirect()->route('admin.game-money.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        $gameUserId = 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT);
        
        // Get money details
        $money = DB::connection('game_mysql')
            ->table('t_money')
            ->where('userid', $gameUserId)
            ->first();

        if (!$money) {
            // Create money record if not exists
            DB::connection('game_mysql')->table('t_money')->insert([
                'userid' => $gameUserId,
                'money' => 0,
                'realmoney' => 0,
                'giftid' => 0,
                'giftjifen' => 0,
                'points' => 0,
                'specjifen' => 0
            ]);

            $money = (object) [
                'userid' => $gameUserId,
                'money' => 0,
                'realmoney' => 0,
                'giftid' => 0,
                'giftjifen' => 0,
                'points' => 0,
                'specjifen' => 0
            ];
        }

        return view('admin.game-money.edit', compact('admin', 'account', 'money'));
    }

    public function update(Request $request, $accountId)
    {
        $admin = Session::get('admin_user');
        
        // Validate input
        $request->validate([
            'realmoney' => 'required|integer|min:0|max:2000000000',
            'money' => 'required|integer|min:0|max:2000000000',
            'action_type' => 'required|in:set,add,subtract',
            'reason' => 'required|string|max:255',
        ]);

        // Get account info
        $account = DB::table('t_account')->where('ID', $accountId)->first();
        if (!$account) {
            return redirect()->route('admin.game-money.index')
                ->withErrors(['error' => 'Không tìm thấy tài khoản.']);
        }

        $gameUserId = 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT);
        
        // Get current money
        $currentMoney = DB::connection('game_mysql')
            ->table('t_money')
            ->where('userid', $gameUserId)
            ->first();

        $oldRealmoney = $currentMoney ? ($currentMoney->realmoney ?? 0) : 0;
        $oldMoney = $currentMoney ? ($currentMoney->money ?? 0) : 0;

        // Calculate new values based on action type
        switch ($request->action_type) {
            case 'set':
                $newRealmoney = $request->realmoney;
                $newMoney = $request->money;
                break;
            case 'add':
                $newRealmoney = $oldRealmoney + $request->realmoney;
                $newMoney = $oldMoney + $request->money;
                break;
            case 'subtract':
                $newRealmoney = max(0, $oldRealmoney - $request->realmoney);
                $newMoney = max(0, $oldMoney - $request->money);
                break;
        }

        // Ensure values don't exceed limits
        $newRealmoney = min(2000000000, max(0, $newRealmoney));
        $newMoney = min(2000000000, max(0, $newMoney));

        // Update or insert money record
        if ($currentMoney) {
            DB::connection('game_mysql')->table('t_money')
                ->where('userid', $gameUserId)
                ->update([
                    'realmoney' => $newRealmoney,
                    'money' => $newMoney
                ]);
        } else {
            DB::connection('game_mysql')->table('t_money')->insert([
                'userid' => $gameUserId,
                'money' => $newMoney,
                'realmoney' => $newRealmoney,
                'giftid' => 0,
                'giftjifen' => 0,
                'points' => 0,
                'specjifen' => 0
            ]);
        }

        // Clear cache
        $this->gameDataService->clearAccountCache($accountId);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'update_game_money',
            'game_money',
            $accountId,
            $account->UserName,
            ['realmoney' => $oldRealmoney, 'money' => $oldMoney],
            ['realmoney' => $newRealmoney, 'money' => $newMoney, 'action_type' => $request->action_type],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.game-money.show', $accountId)
            ->with('success', "Đã cập nhật xu game cho tài khoản {$account->UserName} thành công.");
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
