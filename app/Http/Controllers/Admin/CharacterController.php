<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CharacterController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $searchType = $request->get('search_type', 'character_name');
        $serverFilter = $request->get('server', 'all');

        // Base query for characters from game database
        $query = DB::connection('game_mysql')->table('t_roles')
            ->select([
                'rid',
                'rname as character_name',
                'userid',
                'level',
                'serverid',
                'occupation',
                'experience',
                'money',
                'regtime',
                'lasttime',
                'logofftime',
                'isdel'
            ])
            ->where('isdel', 0); // Only show active characters

        // Apply search filters
        if ($search) {
            switch ($searchType) {
                case 'character_name':
                    $query->where('rname', 'like', "%{$search}%");
                    break;
                case 'character_id':
                    $query->where('rid', $search);
                    break;
                case 'user_id':
                    $query->where('userid', 'like', "%{$search}%");
                    break;
            }
        }

        // Apply server filter
        if ($serverFilter !== 'all') {
            $query->where('serverid', $serverFilter);
        }

        $characters = $query->orderBy('regtime', 'desc')->paginate(20);

        // Enhance characters with account info
        foreach ($characters as $character) {
            // Extract account ID from userid (remove ZT prefix and leading zeros)
            if (preg_match('/^ZT(\d+)$/', $character->userid, $matches)) {
                $accountId = (int)$matches[1];

                // Get account info from website database
                $account = DB::table('t_account')->where('ID', $accountId)->first();
                $character->username = $account ? $account->UserName : 'Unknown';
                $character->email = $account ? $account->Email : 'N/A';
                $character->account_status = $account ? $account->Status : 0;
            } else {
                $character->username = 'Unknown';
                $character->email = 'N/A';
                $character->account_status = 0;
            }
        }

        // Get server list for filter
        $servers = DB::connection('game_mysql')->table('t_roles')
            ->select('serverid')
            ->distinct()
            ->orderBy('serverid')
            ->get();

        return view('admin.characters.index', compact('admin', 'characters', 'search', 'searchType', 'serverFilter', 'servers'));
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        // Get character from game database
        $character = DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->first();

        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Get account info from website database
        if (preg_match('/^ZT(\d+)$/', $character->userid, $matches)) {
            $accountId = (int)$matches[1];
            $account = DB::table('t_account')->where('ID', $accountId)->first();

            $character->username = $account ? $account->UserName : 'Unknown';
            $character->email = $account ? $account->Email : 'N/A';
            $character->account_status = $account ? $account->Status : 0;
        } else {
            $character->username = 'Unknown';
            $character->email = 'N/A';
            $character->account_status = 0;
        }

        // Mock login history for now
        $loginHistory = [];

        return view('admin.characters.show', compact('admin', 'character', 'loginHistory'));
    }

    public function edit($id)
    {
        $admin = Session::get('admin_user');

        // Get character from game database
        $character = DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->first();

        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Get account info from website database
        if (preg_match('/^ZT(\d+)$/', $character->userid, $matches)) {
            $accountId = (int)$matches[1];
            $account = DB::table('t_account')->where('ID', $accountId)->first();

            $character->username = $account ? $account->UserName : 'Unknown';
            $character->email = $account ? $account->Email : 'N/A';
        } else {
            $character->username = 'Unknown';
            $character->email = 'N/A';
        }

        return view('admin.characters.edit', compact('admin', 'character'));
    }

    public function update(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'level' => 'required|integer|min:1|max:400',
            'experience' => 'required|integer|min:0',
            'money' => 'required|integer|min:0',
            'occupation' => 'required|integer|min:0|max:10',
        ]);

        // Get character info before update
        $character = DB::connection('game_mysql')->table('t_roles')->where('rid', $id)->first();
        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        $oldData = [
            'level' => $character->level,
            'experience' => $character->experience,
            'money' => $character->money,
            'occupation' => $character->occupation,
        ];

        $newData = [
            'level' => $request->level,
            'experience' => $request->experience,
            'money' => $request->money,
            'occupation' => $request->occupation,
        ];

        // Update character
        DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->update([
                'level' => $request->level,
                'experience' => $request->experience,
                'money' => $request->money,
                'occupation' => $request->occupation,
                'lasttime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'edit_character',
            'character',
            $id,
            $character->rname,
            $oldData,
            $newData,
            'Cập nhật thông tin nhân vật',
            $request->ip()
        );

        return redirect()->route('admin.characters.show', $id)
            ->with('success', "Đã cập nhật thông tin nhân vật {$character->rname} thành công.");
    }

    public function ban(Request $request, $id)
    {
        $admin = Session::get('admin_user');
        $reason = $request->input('reason', 'Vi phạm quy định');

        // Get character info before ban
        $character = DB::connection('game_mysql')->table('t_roles')->where('rid', $id)->first();
        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Update character status (isdel = 1 means banned/deleted)
        DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->update([
                'isdel' => 1,
                'lasttime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'ban_character',
            'character',
            $id,
            $character->rname,
            ['isdel' => $character->isdel],
            ['isdel' => 1],
            $reason,
            $request->ip()
        );

        return redirect()->route('admin.characters.show', $id)
            ->with('success', "Đã khóa nhân vật {$character->rname}. Lý do: {$reason}");
    }

    public function unban($id)
    {
        $admin = Session::get('admin_user');

        // Get character info before unban
        $character = DB::connection('game_mysql')->table('t_roles')->where('rid', $id)->first();
        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Update character status (isdel = 0 means active)
        DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->update([
                'isdel' => 0,
                'lasttime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'unban_character',
            'character',
            $id,
            $character->rname,
            ['isdel' => $character->isdel],
            ['isdel' => 0],
            'Mở khóa nhân vật',
            request()->ip()
        );

        return redirect()->route('admin.characters.show', $id)
            ->with('success', "Đã mở khóa nhân vật {$character->rname} thành công.");
    }

    public function destroy($id)
    {
        $admin = Session::get('admin_user');

        // Get character info before delete
        $character = DB::connection('game_mysql')->table('t_roles')->where('rid', $id)->first();
        if (!$character) {
            return redirect()->route('admin.characters.index')->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Soft delete character (set isdel = 1)
        DB::connection('game_mysql')->table('t_roles')
            ->where('rid', $id)
            ->update([
                'isdel' => 1,
                'lasttime' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'delete_character',
            'character',
            $id,
            $character->rname,
            ['isdel' => $character->isdel],
            ['isdel' => 1],
            'Xóa nhân vật',
            request()->ip()
        );

        return redirect()->route('admin.characters.index')
            ->with('success', "Đã xóa nhân vật {$character->rname} thành công.");
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
