<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Find user by username or email
        $user = UserAccount::where(function ($query) use ($request) {
            $query->where('username', $request->username)
                ->orWhere('email', $request->username);
        })->where('status', 'active')->first();

        // Debug logging
        Log::info('Login attempt', [
            'username' => $request->username,
            'user_found' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'password_check' => $user ? Hash::check($request->password, $user->password) : false
        ]);

        if ($user && Hash::check($request->password, $user->password)) {
            // Login successful
            Session::put('user_account', [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'game_account_id' => $user->game_account_id,
                'status' => $user->status,
            ]);

            // Update last login (optional)
            $user->update([
                'updated_at' => now()
            ]);

            return redirect('/user/dashboard')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'login' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ])->withInput();
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
            'username' => 'required|string|min:3|max:50|unique:user_accounts,username',
            'email' => 'required|email|max:100|unique:user_accounts,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'game_username' => 'nullable|string|max:50', // Optional link to game account
        ]);

        // Check if game account exists (if provided)
        $gameAccountId = null;
        if ($request->game_username) {
            $gameAccount = DB::table('game_accounts')
                ->where('username', $request->game_username)
                ->first();

            if (!$gameAccount) {
                return back()->withErrors([
                    'game_username' => 'Không tìm thấy tài khoản game với username này.'
                ])->withInput();
            }

            // Check if game account is already linked
            $existingLink = UserAccount::where('game_account_id', $gameAccount->id)->first();
            if ($existingLink) {
                return back()->withErrors([
                    'game_username' => 'Tài khoản game này đã được liên kết với tài khoản khác.'
                ])->withInput();
            }

            $gameAccountId = $gameAccount->id;
        }

        // Create user account
        $user = UserAccount::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, // Will be hashed by mutator
            'phone' => $request->phone,
            'game_account_id' => $gameAccountId,
            'status' => 'active',
        ]);

        // Create initial coin balance
        UserCoinBalance::create([
            'user_id' => $user->id,
            'web_coins' => 0,
            'game_coins' => 0,
            'total_recharged' => 0,
        ]);

        // Auto login after registration
        Session::put('user_account', [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'game_account_id' => $user->game_account_id,
            'status' => $user->status,
        ]);

        return redirect('/user/dashboard')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MU Game Portal.');
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
            'email' => 'required|email|exists:user_accounts,email',
        ]);

        // TODO: Implement password reset functionality
        // For now, just show a message
        return back()->with('success', 'Chức năng đặt lại mật khẩu sẽ được cập nhật sớm. Vui lòng liên hệ admin để được hỗ trợ.');
    }
}
