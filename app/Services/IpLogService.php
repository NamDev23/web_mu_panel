<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IpLogService
{
    /**
     * Log user activity with IP
     */
    public static function logActivity($ipAddress, $accountId = null, $username = null, $characterName = null, $action = 'login', $status = 'success', $userAgent = null)
    {
        try {
            // Get location data (mock for now - in production you'd use a GeoIP service)
            $locationData = self::getLocationData($ipAddress);

            DB::table('ip_logs')->insert([
                'ip_address' => $ipAddress,
                'account_id' => $accountId,
                'username' => $username,
                'character_name' => $characterName,
                'action' => $action,
                'status' => $status,
                'user_agent' => $userAgent,
                'country' => $locationData['country'] ?? null,
                'city' => $locationData['city'] ?? null,
                'location_data' => json_encode($locationData),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Check for suspicious activity
            self::checkSuspiciousActivity($ipAddress, $accountId, $username);

        } catch (\Exception $e) {
            Log::error('Error logging IP activity: ' . $e->getMessage());
        }
    }

    /**
     * Check if IP is banned
     */
    public static function isIpBanned($ipAddress)
    {
        $ban = DB::table('banned_ips')
            ->where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        return $ban !== null;
    }

    /**
     * Check for suspicious activity and create alerts
     */
    private static function checkSuspiciousActivity($ipAddress, $accountId, $username)
    {
        // Check for multiple accounts from same IP
        $uniqueAccounts = DB::table('ip_logs')
            ->where('ip_address', $ipAddress)
            ->whereDate('created_at', today())
            ->distinct('account_id')
            ->count();

        if ($uniqueAccounts >= 5) {
            self::createAlert($ipAddress, 'multiple_accounts', 'Multiple Accounts', 
                "IP {$ipAddress} đã đăng nhập {$uniqueAccounts} tài khoản khác nhau trong ngày", 2);
        }

        // Check for rapid login attempts
        $recentLogins = DB::table('ip_logs')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentLogins >= 10) {
            self::createAlert($ipAddress, 'rapid_login', 'Rapid Login Attempts', 
                "IP {$ipAddress} có {$recentLogins} lần đăng nhập trong 5 phút", 3);
        }

        // Check for failed login attempts
        $failedLogins = DB::table('ip_logs')
            ->where('ip_address', $ipAddress)
            ->where('action', 'failed_login')
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($failedLogins >= 5) {
            self::createAlert($ipAddress, 'failed_attempts', 'Multiple Failed Logins', 
                "IP {$ipAddress} có {$failedLogins} lần đăng nhập thất bại trong 1 giờ", 3);
        }
    }

    /**
     * Create security alert
     */
    private static function createAlert($ipAddress, $alertType, $title, $description, $severity = 1)
    {
        // Check if alert already exists for this IP and type today
        $existingAlert = DB::table('ip_alerts')
            ->where('ip_address', $ipAddress)
            ->where('alert_type', $alertType)
            ->whereDate('created_at', today())
            ->first();

        if (!$existingAlert) {
            DB::table('ip_alerts')->insert([
                'ip_address' => $ipAddress,
                'alert_type' => $alertType,
                'title' => $title,
                'description' => $description,
                'severity' => $severity,
                'alert_data' => json_encode([
                    'detected_at' => now(),
                    'ip_address' => $ipAddress
                ]),
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Get location data for IP (mock implementation)
     */
    private static function getLocationData($ipAddress)
    {
        // In production, you would use a service like MaxMind GeoIP2 or similar
        // For now, return mock data
        
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return [
                'country' => 'VN',
                'city' => 'Local',
                'latitude' => null,
                'longitude' => null,
                'isp' => 'Local'
            ];
        }

        // Mock data for other IPs
        return [
            'country' => 'VN',
            'city' => 'Ho Chi Minh City',
            'latitude' => 10.8231,
            'longitude' => 106.6297,
            'isp' => 'Unknown ISP'
        ];
    }

    /**
     * Get IP statistics
     */
    public static function getIpStatistics($ipAddress)
    {
        return [
            'total_logins' => DB::table('ip_logs')
                ->where('ip_address', $ipAddress)
                ->count(),
            'unique_users' => DB::table('ip_logs')
                ->where('ip_address', $ipAddress)
                ->distinct('username')
                ->count(),
            'failed_logins' => DB::table('ip_logs')
                ->where('ip_address', $ipAddress)
                ->where('action', 'failed_login')
                ->count(),
            'first_seen' => DB::table('ip_logs')
                ->where('ip_address', $ipAddress)
                ->min('created_at'),
            'last_seen' => DB::table('ip_logs')
                ->where('ip_address', $ipAddress)
                ->max('created_at'),
            'is_banned' => self::isIpBanned($ipAddress)
        ];
    }

    /**
     * Clean old logs (run this in a scheduled job)
     */
    public static function cleanOldLogs($daysToKeep = 90)
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $deletedCount = DB::table('ip_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        Log::info("Cleaned {$deletedCount} old IP logs older than {$daysToKeep} days");
        
        return $deletedCount;
    }

    /**
     * Get top IPs by activity
     */
    public static function getTopIps($limit = 10, $days = 7)
    {
        return DB::table('ip_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(*) as login_count'),
                DB::raw('COUNT(DISTINCT username) as user_count'),
                DB::raw('MAX(created_at) as last_activity')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('ip_address')
            ->orderBy('login_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get alerts for dashboard
     */
    public static function getRecentAlerts($limit = 10)
    {
        return DB::table('ip_alerts')
            ->where('status', 'new')
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
