<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
        $admin = DB::table('admin_users')
            ->where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Login successful
            Session::put('admin_user', [
                'id' => $admin->id,
                'username' => $admin->username,
                'email' => $admin->email,
                'full_name' => $admin->full_name,
                'role' => $admin->role,
                'permissions' => json_decode($admin->permissions, true),
            ]);

            // Update last login
            DB::table('admin_users')
                ->where('id', $admin->id)
                ->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                    'updated_at' => now(),
                ]);

            return redirect('/admin/dashboard')->with('success', 'Đăng nhập thành công!');
        }

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
            'accounts' => DB::table('game_accounts')->count(),
            'characters' => DB::table('t_roles')->count(),
            'giftcodes' => DB::table('giftcodes')->count(),
            'total_coins' => DB::table('recharge_logs')->sum('coins_added') ?? 0,
        ];

        return view('dashboard', compact('admin', 'stats'));
    }
}
