<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class IpManagementController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');
        $actionFilter = $request->get('action', 'all');

        // Base query for IP logs
        $query = DB::table('ip_logs as i')
            ->leftJoin('t_account as a', 'i.account_id', '=', 'a.ID')
            ->select([
                'i.*',
                'a.UserName',
                'a.Email as email',
                'a.Status as account_status'
            ]);

        // Apply search filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('i.ip_address', 'like', "%{$search}%")
                    ->orWhere('i.username', 'like', "%{$search}%")
                    ->orWhere('i.character_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('i.status', $statusFilter);
        }

        // Apply action filter
        if ($actionFilter !== 'all') {
            $query->where('i.action', $actionFilter);
        }

        $ipLogs = $query->orderBy('i.created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_ips' => DB::table('ip_logs')->distinct('ip_address')->count('ip_address'),
            'unique_today' => DB::table('ip_logs')
                ->whereDate('created_at', today())
                ->distinct('ip_address')
                ->count('ip_address'),
            'banned_ips' => DB::table('banned_ips')->count(),
            'suspicious_ips' => DB::table('ip_logs')
                ->select('ip_address', DB::raw('COUNT(DISTINCT username) as user_count'))
                ->groupBy('ip_address')
                ->havingRaw('COUNT(DISTINCT username) > 5')
                ->count()
        ];

        // Get top IPs
        $topIps = DB::table('ip_logs')
            ->select('ip_address', DB::raw('COUNT(*) as login_count'), DB::raw('COUNT(DISTINCT username) as user_count'))
            ->groupBy('ip_address')
            ->orderBy('login_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ip-management.index', compact('admin', 'ipLogs', 'search', 'statusFilter', 'actionFilter', 'stats', 'topIps'));
    }

    public function show($ip)
    {
        $admin = Session::get('admin_user');

        // Get IP details
        $ipDetails = DB::table('ip_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(*) as total_logins'),
                DB::raw('COUNT(DISTINCT username) as unique_users'),
                DB::raw('MIN(created_at) as first_seen'),
                DB::raw('MAX(created_at) as last_seen')
            )
            ->where('ip_address', $ip)
            ->groupBy('ip_address')
            ->first();

        if (!$ipDetails) {
            return redirect()->route('admin.ip-management.index')->withErrors(['error' => 'Không tìm thấy IP này.']);
        }

        // Get users from this IP
        $users = DB::table('ip_logs as i')
            ->leftJoin('t_account as a', 'i.account_id', '=', 'a.ID')
            ->select(
                'i.username',
                'a.Email as email',
                'a.Status',
                DB::raw('COUNT(*) as login_count'),
                DB::raw('MAX(i.created_at) as last_login')
            )
            ->where('i.ip_address', $ip)
            ->groupBy('i.username', 'a.Email', 'a.Status')
            ->orderBy('login_count', 'desc')
            ->get();

        // Get recent activities
        $activities = DB::table('ip_logs')
            ->where('ip_address', $ip)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Check if IP is banned
        $isBanned = DB::table('banned_ips')->where('ip_address', $ip)->exists();

        return view('admin.ip-management.show', compact('admin', 'ipDetails', 'users', 'activities', 'isBanned'));
    }

    public function banIp(Request $request, $ip)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'reason' => 'required|string|max:500',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Check if IP is already banned
        $existingBan = DB::table('banned_ips')->where('ip_address', $ip)->first();
        if ($existingBan) {
            return redirect()->back()->withErrors(['error' => 'IP này đã bị cấm trước đó.']);
        }

        // Ban the IP
        DB::table('banned_ips')->insert([
            'ip_address' => $ip,
            'reason' => $request->reason,
            'type' => $request->expires_at ? 'temporary' : 'permanent',
            'banned_by' => $admin['id'],
            'banned_by_username' => $admin['username'],
            'banned_at' => now(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'ban_ip',
            'ip',
            $ip,
            $ip,
            ['status' => 'active'],
            ['status' => 'banned', 'reason' => $request->reason],
            $request->reason,
            $request->ip()
        );

        return redirect()->route('admin.ip-management.show', $ip)
            ->with('success', "Đã cấm IP {$ip}. Lý do: {$request->reason}");
    }

    public function unbanIp($ip)
    {
        $admin = Session::get('admin_user');

        // Check if IP is banned
        $ban = DB::table('banned_ips')->where('ip_address', $ip)->first();
        if (!$ban) {
            return redirect()->back()->withErrors(['error' => 'IP này không bị cấm.']);
        }

        // Unban the IP
        DB::table('banned_ips')->where('ip_address', $ip)->delete();

        // Log admin action
        $this->logAdminAction(
            $admin,
            'unban_ip',
            'ip',
            $ip,
            $ip,
            ['status' => 'banned'],
            ['status' => 'active'],
            'Bỏ cấm IP',
            request()->ip()
        );

        return redirect()->route('admin.ip-management.show', $ip)
            ->with('success', "Đã bỏ cấm IP {$ip} thành công.");
    }

    public function bannedIps(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');

        // Base query for banned IPs
        $query = DB::table('banned_ips as b')
            ->leftJoin('admin_users as a', 'b.banned_by', '=', 'a.id')
            ->select([
                'b.*',
                'a.username as admin_username'
            ]);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('b.ip_address', 'like', "%{$search}%")
                    ->orWhere('b.reason', 'like', "%{$search}%");
            });
        }

        $bannedIps = $query->orderBy('b.banned_at', 'desc')->paginate(20);

        return view('admin.ip-management.banned', compact('admin', 'bannedIps', 'search'));
    }

    public function suspicious(Request $request)
    {
        $admin = Session::get('admin_user');
        $threshold = $request->get('threshold', 5); // Minimum users per IP to be considered suspicious

        // Get suspicious IPs (multiple users from same IP)
        $suspiciousIps = DB::table('ip_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(DISTINCT username) as user_count'),
                DB::raw('COUNT(*) as login_count'),
                DB::raw('MAX(created_at) as last_activity')
            )
            ->groupBy('ip_address')
            ->havingRaw('COUNT(DISTINCT username) >= ?', [$threshold])
            ->orderBy('user_count', 'desc')
            ->paginate(20);

        return view('admin.ip-management.suspicious', compact('admin', 'suspiciousIps', 'threshold'));
    }

    public function export(Request $request)
    {
        $admin = Session::get('admin_user');
        $type = $request->get('type', 'all');

        switch ($type) {
            case 'banned':
                return $this->exportBannedIps();
            case 'suspicious':
                return $this->exportSuspiciousIps();
            default:
                return $this->exportAllIps();
        }
    }

    private function exportAllIps()
    {
        $ips = DB::table('ip_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(*) as login_count'),
                DB::raw('COUNT(DISTINCT username) as user_count'),
                DB::raw('MIN(created_at) as first_seen'),
                DB::raw('MAX(created_at) as last_seen')
            )
            ->groupBy('ip_address')
            ->orderBy('login_count', 'desc')
            ->get();

        $filename = 'ip_logs_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($ips) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['IP Address', 'Login Count', 'User Count', 'First Seen', 'Last Seen']);

            foreach ($ips as $ip) {
                fputcsv($file, [
                    $ip->ip_address,
                    $ip->login_count,
                    $ip->user_count,
                    $ip->first_seen,
                    $ip->last_seen
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportBannedIps()
    {
        $bannedIps = DB::table('banned_ips as b')
            ->leftJoin('admin_users as a', 'b.banned_by', '=', 'a.id')
            ->select('b.*', 'a.username as admin_username')
            ->get();

        $filename = 'banned_ips_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($bannedIps) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['IP Address', 'Reason', 'Banned By', 'Banned At', 'Expires At']);

            foreach ($bannedIps as $ip) {
                fputcsv($file, [
                    $ip->ip_address,
                    $ip->reason,
                    $ip->admin_username,
                    $ip->banned_at,
                    $ip->expires_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportSuspiciousIps()
    {
        $suspiciousIps = DB::table('ip_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(DISTINCT username) as user_count'),
                DB::raw('COUNT(*) as login_count'),
                DB::raw('MAX(created_at) as last_activity')
            )
            ->groupBy('ip_address')
            ->havingRaw('COUNT(DISTINCT username) >= 5')
            ->orderBy('user_count', 'desc')
            ->get();

        $filename = 'suspicious_ips_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($suspiciousIps) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['IP Address', 'User Count', 'Login Count', 'Last Activity']);

            foreach ($suspiciousIps as $ip) {
                fputcsv($file, [
                    $ip->ip_address,
                    $ip->user_count,
                    $ip->login_count,
                    $ip->last_activity
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
