<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminLogsController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $adminFilter = $request->get('admin_filter', 'all');
        $actionFilter = $request->get('action_filter', 'all');
        $targetFilter = $request->get('target_filter', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query for admin action logs
        $query = DB::table('admin_action_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select([
                'l.*',
                'a.username as admin_username',
                'a.email as admin_email'
            ]);

        // Apply search filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('l.target_name', 'like', "%{$search}%")
                  ->orWhere('l.reason', 'like', "%{$search}%")
                  ->orWhere('l.admin_username', 'like', "%{$search}%");
            });
        }

        // Apply admin filter
        if ($adminFilter !== 'all') {
            $query->where('l.admin_id', $adminFilter);
        }

        // Apply action filter
        if ($actionFilter !== 'all') {
            $query->where('l.action', $actionFilter);
        }

        // Apply target type filter
        if ($targetFilter !== 'all') {
            $query->where('l.target_type', $targetFilter);
        }

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('l.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('l.created_at', '<=', $dateTo);
        }

        $logs = $query->orderBy('l.created_at', 'desc')->paginate(20);

        // Get filter options
        $admins = DB::table('admin_users')->select('id', 'username')->get();
        $actions = DB::table('admin_action_logs')->distinct()->pluck('action');
        $targetTypes = DB::table('admin_action_logs')->distinct()->pluck('target_type');

        // Get statistics
        $stats = [
            'total_actions' => DB::table('admin_action_logs')->count(),
            'today_actions' => DB::table('admin_action_logs')
                ->whereDate('created_at', today())
                ->count(),
            'unique_admins' => DB::table('admin_action_logs')
                ->distinct('admin_id')
                ->count('admin_id'),
            'most_active_admin' => DB::table('admin_action_logs as l')
                ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
                ->select('a.username', DB::raw('COUNT(*) as action_count'))
                ->groupBy('l.admin_id', 'a.username')
                ->orderBy('action_count', 'desc')
                ->first()
        ];

        return view('admin.admin-logs.index', compact(
            'admin', 'logs', 'search', 'adminFilter', 'actionFilter', 'targetFilter', 
            'dateFrom', 'dateTo', 'admins', 'actions', 'targetTypes', 'stats'
        ));
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');
        
        $log = DB::table('admin_action_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select([
                'l.*',
                'a.username as admin_username',
                'a.email as admin_email'
            ])
            ->where('l.id', $id)
            ->first();

        if (!$log) {
            return redirect()->route('admin.admin-logs.index')->withErrors(['error' => 'Không tìm thấy log này.']);
        }

        // Decode JSON data
        $log->old_data = json_decode($log->old_data, true);
        $log->new_data = json_decode($log->new_data, true);

        return view('admin.admin-logs.show', compact('admin', 'log'));
    }

    public function export(Request $request)
    {
        $admin = Session::get('admin_user');
        $adminFilter = $request->get('admin_filter', 'all');
        $actionFilter = $request->get('action_filter', 'all');
        $targetFilter = $request->get('target_filter', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $query = DB::table('admin_action_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select([
                'l.id',
                'a.username as admin_username',
                'l.action',
                'l.target_type',
                'l.target_name',
                'l.reason',
                'l.ip_address',
                'l.created_at'
            ]);

        // Apply filters
        if ($adminFilter !== 'all') {
            $query->where('l.admin_id', $adminFilter);
        }
        if ($actionFilter !== 'all') {
            $query->where('l.action', $actionFilter);
        }
        if ($targetFilter !== 'all') {
            $query->where('l.target_type', $targetFilter);
        }
        if ($dateFrom) {
            $query->whereDate('l.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('l.created_at', '<=', $dateTo);
        }

        $logs = $query->orderBy('l.created_at', 'desc')->get();

        $filename = 'admin_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['ID', 'Admin', 'Action', 'Target Type', 'Target Name', 'Reason', 'IP Address', 'Created At']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->admin_username,
                    $log->action,
                    $log->target_type,
                    $log->target_name,
                    $log->reason,
                    $log->ip_address,
                    $log->created_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function statistics(Request $request)
    {
        $admin = Session::get('admin_user');
        $period = $request->get('period', '30'); // days
        
        $startDate = now()->subDays($period);
        $endDate = now();

        // Actions by day
        $actionsByDay = DB::table('admin_action_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Actions by admin
        $actionsByAdmin = DB::table('admin_action_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select('a.username', DB::raw('COUNT(*) as count'))
            ->whereBetween('l.created_at', [$startDate, $endDate])
            ->groupBy('l.admin_id', 'a.username')
            ->orderBy('count', 'desc')
            ->get();

        // Actions by type
        $actionsByType = DB::table('admin_action_logs')
            ->select('action', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Target types
        $targetTypes = DB::table('admin_action_logs')
            ->select('target_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('target_type')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'actions_by_day' => $actionsByDay,
            'actions_by_admin' => $actionsByAdmin,
            'actions_by_type' => $actionsByType,
            'target_types' => $targetTypes
        ]);
    }

    public function loginLogs(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');

        // Base query for admin login logs
        $query = DB::table('admin_login_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select([
                'l.*',
                'a.username as admin_username',
                'a.email as admin_email'
            ]);

        // Apply search filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('a.username', 'like', "%{$search}%")
                  ->orWhere('l.ip_address', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('l.status', $statusFilter);
        }

        $loginLogs = $query->orderBy('l.created_at', 'desc')->paginate(20);

        // Get statistics
        $loginStats = [
            'total_logins' => DB::table('admin_login_logs')->count(),
            'successful_logins' => DB::table('admin_login_logs')->where('status', 'success')->count(),
            'failed_logins' => DB::table('admin_login_logs')->where('status', 'failed')->count(),
            'today_logins' => DB::table('admin_login_logs')->whereDate('created_at', today())->count()
        ];

        return view('admin.admin-logs.login-logs', compact('admin', 'loginLogs', 'search', 'statusFilter', 'loginStats'));
    }
}
