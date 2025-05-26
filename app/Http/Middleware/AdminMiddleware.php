<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
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
        // Check if admin is logged in via session
        $admin = Session::get('admin_user');

        if (!$admin) {
            // If it's an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.'
                ], 401);
            }

            // Otherwise redirect to login page
            return redirect('/admin/login')->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        // Check if admin has proper role (admin or super_admin)
        if (!in_array($admin['role'], ['admin', 'super_admin'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập tính năng này.'
                ], 403);
            }

            return redirect('/admin/login')->withErrors(['error' => 'Bạn không có quyền truy cập tính năng này.']);
        }

        return $next($request);
    }
}
