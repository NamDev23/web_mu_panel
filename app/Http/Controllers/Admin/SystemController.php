<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SystemController extends Controller
{
    protected $gameDataService;

    public function __construct(GameDataService $gameDataService)
    {
        $this->gameDataService = $gameDataService;
    }

    public function performance()
    {
        $admin = Session::get('admin_user');

        // Get database performance stats
        $stats = [
            'website_db' => $this->getDatabaseStats('mysql'),
            'game_db' => $this->getDatabaseStats('game_mysql'),
            'cache_stats' => $this->getCacheStats(),
            'system_info' => $this->getSystemInfo(),
        ];

        return view('admin.system.performance', compact('admin', 'stats'));
    }

    public function clearCache(Request $request)
    {
        $admin = Session::get('admin_user');
        $type = $request->get('type', 'all');

        switch ($type) {
            case 'game_data':
                $this->gameDataService->clearAllCache();
                $message = 'Đã xóa cache dữ liệu game';
                break;
            case 'all':
            default:
                Cache::flush();
                $message = 'Đã xóa toàn bộ cache';
                break;
        }

        // Log admin action
        DB::table('admin_action_logs')->insert([
            'admin_id' => $admin['id'],
            'admin_username' => $admin['username'],
            'action' => 'clear_cache',
            'target_type' => 'system',
            'target_id' => 0,
            'target_name' => $type,
            'old_data' => json_encode([]),
            'new_data' => json_encode(['type' => $type]),
            'reason' => 'Xóa cache hệ thống',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.system.performance')
            ->with('success', $message);
    }

    private function getDatabaseStats($connection)
    {
        try {
            $stats = [];
            
            // Get connection info
            $pdo = DB::connection($connection)->getPdo();
            $stats['connection'] = 'Connected';
            
            // Get database size (approximate)
            if ($connection === 'mysql') {
                $tables = ['t_account', 'admin_users', 'admin_action_logs'];
            } else {
                $tables = ['t_roles', 't_money'];
            }
            
            $totalRows = 0;
            foreach ($tables as $table) {
                try {
                    $count = DB::connection($connection)->table($table)->count();
                    $stats['tables'][$table] = $count;
                    $totalRows += $count;
                } catch (\Exception $e) {
                    $stats['tables'][$table] = 'Error';
                }
            }
            
            $stats['total_rows'] = $totalRows;
            
            // Get recent query time (mock)
            $start = microtime(true);
            DB::connection($connection)->select('SELECT 1');
            $stats['query_time'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
            
        } catch (\Exception $e) {
            $stats = [
                'connection' => 'Error: ' . $e->getMessage(),
                'tables' => [],
                'total_rows' => 0,
                'query_time' => 'N/A'
            ];
        }
        
        return $stats;
    }

    private function getCacheStats()
    {
        try {
            // This is a simplified cache stats - in production you might want to use Redis info
            $stats = [
                'driver' => config('cache.default'),
                'status' => 'Active',
            ];
            
            // Test cache performance
            $start = microtime(true);
            Cache::put('test_key', 'test_value', 1);
            $value = Cache::get('test_key');
            Cache::forget('test_key');
            $stats['response_time'] = round((microtime(true) - $start) * 1000, 2) . 'ms';
            
            return $stats;
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'Error: ' . $e->getMessage(),
                'response_time' => 'N/A'
            ];
        }
    }

    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
        ];
    }

    public function logs(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $action = $request->get('action');
        $adminFilter = $request->get('admin_filter');

        $query = DB::table('admin_action_logs')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('target_name', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($adminFilter) {
            $query->where('admin_username', $adminFilter);
        }

        $logs = $query->paginate(50);

        // Get available actions and admins for filters
        $actions = DB::table('admin_action_logs')
            ->select('action')
            ->distinct()
            ->pluck('action');

        $admins = DB::table('admin_action_logs')
            ->select('admin_username')
            ->distinct()
            ->pluck('admin_username');

        return view('admin.system.logs', compact('admin', 'logs', 'search', 'action', 'adminFilter', 'actions', 'admins'));
    }
}
