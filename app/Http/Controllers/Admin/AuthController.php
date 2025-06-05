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
        $admin = DB::table('admin_users')
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
            DB::table('admin_users')
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
            'characters' => 0,
            'giftcodes' => 0,
            'total_coins' => 0,
        ];

        // Get characters count from game database
        try {
            $stats['characters'] = DB::connection('game_mysql')
                ->table('t_roles')
                ->where('isdel', 0) // Only active characters
                ->count();
        } catch (\Exception $e) {
            $stats['characters'] = 0;
        }

        // Get giftcodes count
        try {
            $stats['giftcodes'] = DB::table('giftcodes')->count();
        } catch (\Exception $e) {
            $stats['giftcodes'] = 0;
        }

        // Get total coins from game database
        try {
            $totalCoins = DB::connection('game_mysql')
                ->table('t_money')
                ->sum('YuanBao');
            $stats['total_coins'] = $totalCoins ?? 0;
        } catch (\Exception $e) {
            $stats['total_coins'] = 0;
        }

        return view('dashboard', compact('admin', 'stats'));
    }
}
