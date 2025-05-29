<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\MonthlyCardPurchase;
use App\Models\UserTransactionLog;

class MonthlyCardController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        // Get available packages
        $availablePackages = MonthlyCardPurchase::getAvailablePackages();

        // Get user's active monthly cards
        $activeCards = MonthlyCardPurchase::where('user_id', $user['id'])
            ->active()
            ->orderBy('expires_at', 'desc')
            ->get();

        // Get purchase history
        $purchaseHistory = MonthlyCardPurchase::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.monthly-cards.index', compact(
            'userAccount',
            'availablePackages',
            'activeCards',
            'purchaseHistory'
        ));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'package_key' => 'required|string|in:basic_30,premium_30,vip_30',
        ]);

        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        // Get package info
        $packageInfo = MonthlyCardPurchase::getPackageInfo($request->package_key);
        if (!$packageInfo) {
            return back()->withErrors(['error' => 'Gói thẻ tháng không hợp lệ.']);
        }

        // Check if user has enough coins
        if (!$userAccount->hasEnoughCoins($packageInfo['cost_coins'])) {
            return back()->withErrors(['error' => "Không đủ coin. Cần {$packageInfo['cost_coins']} coin để mua gói này."]);
        }

        // Check if user already has active card of same type
        $existingCard = MonthlyCardPurchase::where('user_id', $user['id'])
            ->where('package_type', $packageInfo['type'])
            ->active()
            ->first();

        if ($existingCard) {
            return back()->withErrors(['error' => "Bạn đã có thẻ tháng {$packageInfo['name']} đang hoạt động."]);
        }

        try {
            DB::beginTransaction();

            // Deduct coins
            $userAccount->deductCoins(
                $packageInfo['cost_coins'],
                "Mua {$packageInfo['name']}",
                null
            );

            // Create monthly card purchase
            $monthlyCard = MonthlyCardPurchase::create([
                'user_id' => $user['id'],
                'package_name' => $packageInfo['name'],
                'package_type' => $packageInfo['type'],
                'duration_days' => $packageInfo['duration_days'],
                'cost_coins' => $packageInfo['cost_coins'],
                'daily_reward_coins' => $packageInfo['daily_reward_coins'],
                'bonus_items' => $packageInfo['bonus_items'],
                'daily_items' => $packageInfo['daily_items'],
                'status' => 'active',
                'activated_at' => now(),
                'expires_at' => now()->addDays($packageInfo['duration_days']),
                'ip_address' => request()->ip()
            ]);

            // Give bonus items immediately
            if (!empty($packageInfo['bonus_items'])) {
                $userAccount->addCoins(
                    0, // No additional coins, just for logging bonus items
                    "Nhận bonus items từ {$packageInfo['name']}",
                    false,
                    $monthlyCard
                );
            }

            // Log service purchase
            UserTransactionLog::logServicePurchase(
                $user['id'],
                'monthly_card',
                $packageInfo['cost_coins'],
                [
                    'package_key' => $request->package_key,
                    'package_name' => $packageInfo['name'],
                    'package_type' => $packageInfo['type'],
                    'duration_days' => $packageInfo['duration_days'],
                    'bonus_items' => $packageInfo['bonus_items']
                ]
            );

            DB::commit();

            return back()->with('success', 
                "Đã mua {$packageInfo['name']} thành công! " .
                "Trừ {$packageInfo['cost_coins']} coin. " .
                "Bạn sẽ nhận {$packageInfo['daily_reward_coins']} coin mỗi ngày trong {$packageInfo['duration_days']} ngày."
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function claimDaily(Request $request, $cardId)
    {
        $user = Session::get('user_account');
        
        $monthlyCard = MonthlyCardPurchase::where('user_id', $user['id'])
            ->where('id', $cardId)
            ->first();

        if (!$monthlyCard) {
            return back()->withErrors(['error' => 'Không tìm thấy thẻ tháng.']);
        }

        if (!$monthlyCard->canClaimToday()) {
            return back()->withErrors(['error' => 'Bạn đã nhận thưởng hôm nay hoặc thẻ tháng đã hết hạn.']);
        }

        try {
            $monthlyCard->claimDailyReward();

            $dailyItemsText = implode(', ', $monthlyCard->daily_items);
            return back()->with('success', 
                "Đã nhận thưởng hàng ngày: {$monthlyCard->daily_reward_coins} coin + {$dailyItemsText}!"
            );

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function history(Request $request)
    {
        $user = Session::get('user_account');
        
        $query = MonthlyCardPurchase::where('user_id', $user['id']);

        // Filter by package type
        if ($request->has('package_type') && !empty($request->package_type)) {
            $query->where('package_type', $request->package_type);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $cards = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('user.monthly-cards.history', compact('cards'));
    }

    public function show($id)
    {
        $user = Session::get('user_account');
        
        $monthlyCard = MonthlyCardPurchase::where('user_id', $user['id'])
            ->where('id', $id)
            ->firstOrFail();

        return view('user.monthly-cards.show', compact('monthlyCard'));
    }
}
