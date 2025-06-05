<?php

namespace App\Http\Middleware;

use App\Services\IpLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LogIpActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log for specific routes
        if ($this->shouldLog($request)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Determine if we should log this request
     */
    private function shouldLog(Request $request): bool
    {
        // Log admin login attempts
        if ($request->is('admin/login') && $request->isMethod('POST')) {
            return true;
        }

        // Log user login attempts (if you have user login)
        if ($request->is('login') && $request->isMethod('POST')) {
            return true;
        }

        // Log admin dashboard access (only once per session per IP)
        if ($request->is('admin/dashboard') && $request->isMethod('GET') && Session::has('admin_user')) {
            return true;
        }

        return false;
    }

    /**
     * Log the activity
     */
    private function logActivity(Request $request, $response)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->header('User-Agent');

        // Determine action and status
        $action = 'login';
        $status = 'success';
        $accountId = null;
        $username = null;

        if ($request->is('admin/login') && $request->isMethod('POST')) {
            $action = 'admin_login';
            $username = $request->input('username');

            // Check if login was successful based on response
            if ($response->isRedirection() && !Session::has('admin_user')) {
                $status = 'failed';
                $action = 'failed_login';
            } else if (Session::has('admin_user')) {
                $admin = Session::get('admin_user');
                $accountId = $admin['id'] ?? null;
                $username = $admin['username'] ?? $username;
            }
        } else if ($request->is('login') && $request->isMethod('POST')) {
            $username = $request->input('username');

            // Check if login was successful
            if ($response->isRedirection() && !Session::has('user')) {
                $status = 'failed';
                $action = 'failed_login';
            }
        } else if ($request->is('admin/dashboard') && Session::has('admin_user')) {
            $action = 'admin_access';
            $admin = Session::get('admin_user');
            $accountId = $admin['id'] ?? null;
            $username = $admin['username'] ?? null;
        }

        // Log the activity
        IpLogService::logActivity(
            $ipAddress,
            $accountId,
            $username,
            null, // character name
            $action,
            $status,
            $userAgent
        );
    }
}
