<?php

namespace App\Http\Controllers\AdminCP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if admin is logged in
        if (!Session::has('admin_user')) {
            return redirect('/admin/login');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        return view('dashboard', compact('stats'));
    }

    private function getDashboardStats()
    {
        try {
            // Get account statistics
            $totalAccounts = DB::table('game_accounts')->count();
            
            // Get character statistics
            $totalCharacters = DB::table('t_roles')->where('isdel', 0)->count();
            
            // Get revenue statistics (coin recharge + monthly cards)
            $coinRevenue = DB::table('recharge_logs')
                ->where('status', 'completed')
                ->sum('amount');
            
            $cardsRevenue = DB::table('monthly_cards')
                ->where('status', '!=', 'cancelled')
                ->sum('price');
            
            $totalRevenue = $coinRevenue + $cardsRevenue;
            
            // Get active giftcodes
            $activeGiftcodes = DB::table('giftcodes')
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->count();

            return [
                'accounts' => $totalAccounts,
                'characters' => $totalCharacters,
                'revenue' => $totalRevenue,
                'giftcodes' => $activeGiftcodes
            ];
        } catch (\Exception $e) {
            // Return default stats if database error
            return [
                'accounts' => 0,
                'characters' => 0,
                'revenue' => 0,
                'giftcodes' => 0
            ];
        }
    }
}
