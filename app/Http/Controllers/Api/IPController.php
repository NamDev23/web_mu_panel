<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IPBan;
use App\Models\LoginLog;
use DB;

class IPController extends Controller
{
    /**
     * Display a listing of IP bans
     */
    public function index(Request $request)
    {
        try {
            $query = IPBan::query();

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('ip_address', 'like', "%{$search}%");
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                switch ($request->status) {
                    case 'active':
                        $query->active();
                        break;
                    case 'expired':
                        $query->expired();
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                }
            }

            $ipBans = $query->with('bannedBy')
                          ->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'ip_bans' => $ipBans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created IP ban
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'ip_address' => 'required|ip',
                'reason' => 'required|string|max:255',
                'duration' => 'nullable|integer|min:1', // Duration in days, null for permanent
            ]);

            // Check if IP is already banned
            $existingBan = IPBan::where('ip_address', $request->ip_address)
                               ->active()
                               ->first();

            if ($existingBan) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP này đã bị ban'
                ], 400);
            }

            $expiresAt = null;
            if ($request->duration) {
                $expiresAt = now()->addDays($request->duration);
            }

            $ipBan = IPBan::create([
                'ip_address' => $request->ip_address,
                'reason' => $request->reason,
                'banned_by' => auth()->id(),
                'banned_at' => now(),
                'expires_at' => $expiresAt,
                'is_active' => true
            ]);

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'ban_ip',
                'target_id' => $ipBan->id,
                'details' => json_encode([
                    'ip_address' => $request->ip_address,
                    'reason' => $request->reason,
                    'duration' => $request->duration
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ban IP thành công',
                'ip_ban' => $ipBan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified IP ban
     */
    public function show($id)
    {
        try {
            $ipBan = IPBan::with('bannedBy')->findOrFail($id);

            // Get login attempts from this IP
            $loginLogs = LoginLog::where('ip_address', $ipBan->ip_address)
                               ->with('user')
                               ->orderBy('login_at', 'desc')
                               ->limit(20)
                               ->get();

            return response()->json([
                'success' => true,
                'ip_ban' => $ipBan,
                'login_logs' => $loginLogs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified IP ban
     */
    public function update(Request $request, $id)
    {
        try {
            $ipBan = IPBan::findOrFail($id);

            $request->validate([
                'reason' => 'required|string|max:255',
                'duration' => 'nullable|integer|min:1',
                'is_active' => 'boolean'
            ]);

            $expiresAt = $ipBan->expires_at;
            if ($request->has('duration')) {
                $expiresAt = $request->duration ? now()->addDays($request->duration) : null;
            }

            $ipBan->update([
                'reason' => $request->reason,
                'expires_at' => $expiresAt,
                'is_active' => $request->has('is_active') ? $request->is_active : $ipBan->is_active
            ]);

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'update_ip_ban',
                'target_id' => $ipBan->id,
                'details' => json_encode([
                    'ip_address' => $ipBan->ip_address,
                    'reason' => $request->reason,
                    'is_active' => $ipBan->is_active
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật IP ban thành công',
                'ip_ban' => $ipBan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified IP ban
     */
    public function destroy($id)
    {
        try {
            $ipBan = IPBan::findOrFail($id);
            
            // Log admin action before deletion
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'delete_ip_ban',
                'target_id' => $ipBan->id,
                'details' => json_encode([
                    'ip_address' => $ipBan->ip_address,
                    'reason' => $ipBan->reason
                ]),
                'created_at' => now()
            ]);

            $ipBan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa IP ban thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban IP
     */
    public function unban($id)
    {
        try {
            $ipBan = IPBan::findOrFail($id);
            $ipBan->is_active = false;
            $ipBan->save();

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'unban_ip',
                'target_id' => $ipBan->id,
                'details' => json_encode([
                    'ip_address' => $ipBan->ip_address
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unban IP thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get IP information and statistics
     */
    public function lookup(Request $request)
    {
        try {
            $request->validate([
                'ip' => 'required|ip'
            ]);

            $ip = $request->ip;

            // Get login statistics for this IP
            $totalLogins = LoginLog::where('ip_address', $ip)->count();
            $successfulLogins = LoginLog::where('ip_address', $ip)->successful()->count();
            $failedLogins = LoginLog::where('ip_address', $ip)->failed()->count();
            $blockedLogins = LoginLog::where('ip_address', $ip)->blocked()->count();

            // Get unique users from this IP
            $uniqueUsers = LoginLog::where('ip_address', $ip)
                                 ->distinct('user_id')
                                 ->count('user_id');

            // Get recent login attempts
            $recentLogins = LoginLog::where('ip_address', $ip)
                                  ->with('user')
                                  ->orderBy('login_at', 'desc')
                                  ->limit(10)
                                  ->get();

            // Check if IP is currently banned
            $currentBan = IPBan::where('ip_address', $ip)->active()->first();

            // Get geolocation info (mock data - integrate with real service)
            $geoInfo = [
                'country' => 'Vietnam',
                'region' => 'Ho Chi Minh City',
                'city' => 'Ho Chi Minh City',
                'isp' => 'Viettel Corporation',
                'timezone' => 'Asia/Ho_Chi_Minh'
            ];

            return response()->json([
                'success' => true,
                'ip_info' => [
                    'ip_address' => $ip,
                    'is_banned' => !is_null($currentBan),
                    'ban_info' => $currentBan,
                    'geo_info' => $geoInfo,
                    'statistics' => [
                        'total_logins' => $totalLogins,
                        'successful_logins' => $successfulLogins,
                        'failed_logins' => $failedLogins,
                        'blocked_logins' => $blockedLogins,
                        'unique_users' => $uniqueUsers,
                        'success_rate' => $totalLogins > 0 ? round(($successfulLogins / $totalLogins) * 100, 2) : 0
                    ],
                    'recent_logins' => $recentLogins
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get IP statistics dashboard
     */
    public function dashboard()
    {
        try {
            $totalBans = IPBan::count();
            $activeBans = IPBan::active()->count();
            $expiredBans = IPBan::expired()->count();
            $inactiveBans = IPBan::where('is_active', false)->count();

            // Get recent bans
            $recentBans = IPBan::with('bannedBy')
                             ->orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

            // Get top banned IPs (most login attempts)
            $topIPs = LoginLog::select('ip_address', DB::raw('count(*) as login_count'))
                            ->groupBy('ip_address')
                            ->orderBy('login_count', 'desc')
                            ->limit(10)
                            ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_bans' => $totalBans,
                    'active_bans' => $activeBans,
                    'expired_bans' => $expiredBans,
                    'inactive_bans' => $inactiveBans
                ],
                'recent_bans' => $recentBans,
                'top_ips' => $topIPs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
