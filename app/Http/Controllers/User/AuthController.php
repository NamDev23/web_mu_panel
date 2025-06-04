<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Account;
use App\Models\UserAccount;
use App\Models\UserCoinBalance;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (Session::has('user_account')) {
            return redirect('/user/dashboard');
        }

        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username or email in t_account table
        $user = Account::where(function ($query) use ($request) {
            $query->where('UserName', $request->username)
                ->orWhere('Email', $request->username);
        })->first();

        // Debug logging
        Log::info('Login attempt', [
            'username' => $request->username,
            'user_found' => $user ? true : false,
            'user_id' => $user ? $user->ID : null,
            'password_check' => $user ? (strtoupper(md5($request->password)) === $user->Password) : false,
            'is_active' => $user ? $user->isActive() : false,
            'status' => $user ? $user->Status : null
        ]);

        // Check if user exists
        if (!$user) {
            Log::info('User login failed - user not found', ['username' => $request->username]);
            return back()->withErrors(['login' => 'Tài khoản không tồn tại.'])->withInput();
        }

        // Check password
        if (strtoupper(md5($request->password)) !== $user->Password) {
            Log::info('User login failed - wrong password', ['username' => $request->username]);
            return back()->withErrors(['login' => 'Mật khẩu không đúng.'])->withInput();
        }

        // Check if account is active
        Log::info('Checking account status', [
            'username' => $request->username,
            'status_value' => $user->Status,
            'status_type' => gettype($user->Status),
            'is_active_result' => $user->isActive(),
            'status_equals_1' => ($user->Status == 1),
            'status_strict_equals_1' => ($user->Status === 1)
        ]);

        if (!$user->isActive()) {
            Log::info('User login failed - account inactive', [
                'username' => $request->username,
                'status' => $user->Status
            ]);
            return back()->withErrors(['login' => 'Tài khoản đã bị khóa. Vui lòng liên hệ admin.'])->withInput();
        }

        // Login successful
        Log::info('User login successful', ['user_id' => $user->ID, 'username' => $user->UserName]);

        Session::put('user_account', [
            'id' => $user->ID,
            'username' => $user->UserName,
            'email' => $user->Email,
            'status' => 'active', // Always set to 'active' for middleware compatibility
        ]);

        return redirect('/user/dashboard')->with('success', 'Đăng nhập thành công!');
    }

    public function showRegister()
    {
        if (Session::has('user_account')) {
            return redirect('/user/dashboard');
        }

        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:4|max:20|alpha_num',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:4|max:32|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Check if username already exists in t_account table
        $existingAccount = Account::where('UserName', $request->username)->first();
        if ($existingAccount) {
            return back()->withErrors([
                'username' => 'Tên đăng nhập đã tồn tại.',
            ])->withInput();
        }

        // Check if email already exists
        $existingEmail = Account::where('Email', $request->email)->first();
        if ($existingEmail) {
            return back()->withErrors([
                'email' => 'Email đã được sử dụng.',
            ])->withInput();
        }

        try {
            // Create account in t_account table
            $account = Account::create([
                'UserName' => $request->username,
                'Password' => strtoupper(md5($request->password)),
                'Email' => $request->email,
                'CreateTime' => now(),
                'LastLoginTime' => now(),
                'Status' => 1, // Active
                'DeviceID' => '',
                'Session' => '',
            ]);

            // Auto login after registration
            Session::put('user_account', [
                'id' => $account->ID,
                'username' => $account->UserName,
                'email' => $account->Email,
                'status' => 'active', // Always set to 'active' for middleware compatibility
            ]);

            return redirect('/user/dashboard')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MU Game Portal.');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors([
                'register' => 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Session::forget('user_account');
        return redirect('/user/login')->with('success', 'Đã đăng xuất thành công!');
    }

    public function showForgotPassword()
    {
        return view('user.auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in t_account table
        $account = Account::where('Email', $request->email)->first();
        if (!$account) {
            return back()->withErrors([
                'email' => 'Email không tồn tại trong hệ thống.',
            ])->withInput();
        }

        // TODO: Implement password reset functionality
        // For now, just show a message
        return back()->with('success', 'Chức năng đặt lại mật khẩu sẽ được cập nhật sớm. Vui lòng liên hệ admin để được hỗ trợ.');
    }
}
