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

        // Get user coins info
        $userCoins = DB::table('user_coins')->where('account_id', $user->ID)->first();
        if (!$userCoins) {
            // Create initial coin record if not exists
            DB::table('user_coins')->insert([
                'account_id' => $user->ID,
                'username' => $user->UserName,
                'coins' => 0,
                'total_recharged' => 0,
                'total_spent' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $userCoins = (object) [
                'coins' => 0,
                'total_recharged' => 0,
                'total_spent' => 0
            ];
        }

        // Get basic stats
        $stats = [
            'username' => $user->UserName,
            'email' => $user->Email,
            'status' => $user->getStatusText(),
            'created_at' => $user->CreateTime,
            'last_login' => $user->LastLoginTime,
            'coins' => $userCoins->coins,
            'total_recharged' => $userCoins->total_recharged,
            'total_spent' => $userCoins->total_spent,
        ];

        return view('user.dashboard', compact('user', 'stats', 'userCoins'));
    }
}
