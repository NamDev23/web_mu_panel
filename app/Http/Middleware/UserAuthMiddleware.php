<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserAuthMiddleware
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
        // Check if user is logged in via session
        $user = Session::get('user_account');

        if (!$user) {
            // If it's an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.'
                ], 401);
            }

            // Otherwise redirect to login page
            return redirect('/user/login')->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        // Check if user account is active
        if ($user['status'] !== 'active') {
            Session::forget('user_account');
            
            $message = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }

            return redirect('/user/login')->withErrors(['error' => $message]);
        }

        return $next($request);
    }
}
