<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Session::has('admin_user')) {
            return redirect('/admin/dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check admin user in database
        $admin = DB::table('t_admin_users')
            ->where('username', $request->username)
            ->where('is_active', 1)
            ->first();

        Log::info('Admin login attempt', [
            'username' => $request->username,
            'admin_found' => $admin ? true : false,
            'password_check' => $admin ? Hash::check($request->password, $admin->password) : false
        ]);

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Login successful
            Log::info('Admin login successful', ['admin_id' => $admin->id, 'username' => $admin->username]);

            Session::put('admin_user', [
                'id' => $admin->id,
                'username' => $admin->username,
                'email' => $admin->email,
                'full_name' => $admin->full_name,
                'role' => $admin->role,
                'permissions' => json_decode($admin->permissions, true),
            ]);

            // Update last login
            DB::table('t_admin_users')
                ->where('id', $admin->id)
                ->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                    'updated_at' => now(),
                ]);

            return redirect('/admin/dashboard')->with('success', 'Đăng nhập thành công!');
        }

        Log::info('Admin login failed', ['username' => $request->username]);

        return back()->withErrors([
            'login' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Session::forget('admin_user');
        return redirect('/admin/login')->with('success', 'Đã đăng xuất thành công!');
    }

    public function dashboard()
    {
        if (!Session::has('admin_user')) {
            return redirect('/admin/login')->withErrors(['login' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        $admin = Session::get('admin_user');

        // Get dashboard stats
        $stats = [
            'accounts' => DB::table('t_account')->count(),
            'characters' => 0, // Will implement later when we have character table
            'giftcodes' => 0, // Will implement later when we have giftcode table
            'total_coins' => 0, // Will implement later
        ];

        return view('dashboard', compact('admin', 'stats'));
    }
}
