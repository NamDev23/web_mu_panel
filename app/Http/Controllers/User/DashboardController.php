<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class DashboardController extends Controller
{
    public function index()
    {
        $userSession = Session::get('user_account');

        if (!$userSession) {
            return redirect('/user/login')->withErrors(['login' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        // Get user from t_account table
        $user = Account::find($userSession['id']);

        if (!$user) {
            return redirect('/user/login')->withErrors(['login' => 'Tài khoản không tồn tại.']);
        }

        // Get basic stats
        $stats = [
            'username' => $user->UserName,
            'email' => $user->Email,
            'status' => $user->getStatusText(),
            'created_at' => $user->CreateTime,
            'last_login' => $user->LastLoginTime,
        ];

        return view('user.dashboard', compact('user', 'stats'));
    }
}
