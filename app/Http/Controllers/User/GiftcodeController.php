<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\GiftcodeUsageLog;
use App\Models\UserTransactionLog;

class GiftcodeController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with(['coinBalance', 'gameAccount'])->find($user['id']);

        // Get giftcode usage history
        $usageHistory = GiftcodeUsageLog::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.giftcode.index', compact(
            'userAccount',
            'usageHistory'
        ));
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'giftcode' => 'required|string|min:4|max:20|regex:/^[A-Z0-9]+$/',
        ]);

        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        if (!$userAccount->game_account_id) {
            return back()->withErrors(['error' => 'Vui lòng liên kết tài khoản game trước khi nhập giftcode.']);
        }

        $giftcode = strtoupper($request->giftcode);

        try {
            DB::beginTransaction();

            // Check if giftcode exists and is valid
            $giftcodeData = $this->validateGiftcode($giftcode, $user['id']);
            
            if (!$giftcodeData['valid']) {
                // Log failed attempt
                GiftcodeUsageLog::logFailure(
                    $user['id'], 
                    $giftcode, 
                    $giftcodeData['error'], 
                    $giftcodeData['status']
                );

                DB::commit();
                return back()->withErrors(['error' => $giftcodeData['error']]);
            }

            // Add rewards to user account
            $totalCoins = 0;
            foreach ($giftcodeData['rewards'] as $reward) {
                if (strpos($reward, 'Coin') !== false) {
                    preg_match('/(\d+)/', $reward, $matches);
                    if (!empty($matches)) {
                        $totalCoins += intval($matches[0]);
                    }
                }
            }

            if ($totalCoins > 0) {
                $userAccount->addCoins(
                    $totalCoins, 
                    "Nhập giftcode: {$giftcode}",
                    false,
                    null
                );
            }

            // Log successful usage
            GiftcodeUsageLog::logSuccess(
                $user['id'],
                $giftcode,
                $giftcodeData['name'],
                $giftcodeData['rewards'],
                $giftcodeData['id']
            );

            // Create transaction log for giftcode
            UserTransactionLog::logGiftcodeRedeem(
                $user['id'],
                $giftcode,
                $giftcodeData['rewards'],
                $giftcodeData['id']
            );

            // Mark giftcode as used (in real implementation)
            $this->markGiftcodeAsUsed($giftcode, $user['id']);

            DB::commit();

            $rewardText = implode(', ', $giftcodeData['rewards']);
            return back()->with('success', 
                "Nhập giftcode '{$giftcode}' thành công! " .
                "Phần thưởng: {$rewardText}"
            );

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log error
            GiftcodeUsageLog::logFailure(
                $user['id'], 
                $giftcode, 
                'Lỗi hệ thống: ' . $e->getMessage()
            );

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi nhập giftcode: ' . $e->getMessage()]);
        }
    }

    public function history(Request $request)
    {
        $user = Session::get('user_account');
        
        $query = GiftcodeUsageLog::where('user_id', $user['id']);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $usages = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('user.giftcode.history', compact('usages'));
    }

    public function getActiveGiftcodes()
    {
        // Mock active giftcodes - in real implementation, get from database
        $giftcodes = [
            [
                'id' => 1,
                'code' => 'WELCOME2025',
                'name' => 'Giftcode chào mừng năm mới',
                'rewards' => ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul'],
                'expires_at' => '2025-02-28',
                'max_uses' => 1000,
                'used_count' => 234,
                'type' => 'public',
                'is_active' => true
            ],
            [
                'id' => 2,
                'code' => 'VIPONLY123',
                'name' => 'Giftcode dành cho VIP',
                'rewards' => ['5000 Coin', '1 Jewel of Life', '20 Jewel of Chaos'],
                'expires_at' => '2025-01-31',
                'max_uses' => 100,
                'used_count' => 67,
                'type' => 'vip',
                'is_active' => true
            ],
            [
                'id' => 3,
                'code' => 'EVENT2025',
                'name' => 'Sự kiện Tết Nguyên Đán',
                'rewards' => ['2000 Coin', '10 Jewel of Bless', '1 Box of Luck'],
                'expires_at' => '2025-02-15',
                'max_uses' => 500,
                'used_count' => 123,
                'type' => 'event',
                'is_active' => true
            ]
        ];

        return response()->json([
            'success' => true,
            'giftcodes' => $giftcodes
        ]);
    }

    // Mock validation method - replace with real giftcode validation
    private function validateGiftcode($giftcode, $userId)
    {
        // Mock giftcode database
        $validGiftcodes = [
            'WELCOME2025' => [
                'id' => 1,
                'name' => 'Giftcode chào mừng năm mới',
                'rewards' => ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul'],
                'expires_at' => '2025-02-28',
                'max_uses' => 1000,
                'used_count' => 234,
                'type' => 'public'
            ],
            'VIPONLY123' => [
                'id' => 2,
                'name' => 'Giftcode dành cho VIP',
                'rewards' => ['5000 Coin', '1 Jewel of Life', '20 Jewel of Chaos'],
                'expires_at' => '2025-01-31',
                'max_uses' => 100,
                'used_count' => 67,
                'type' => 'vip'
            ],
            'EVENT2025' => [
                'id' => 3,
                'name' => 'Sự kiện Tết Nguyên Đán',
                'rewards' => ['2000 Coin', '10 Jewel of Bless', '1 Box of Luck'],
                'expires_at' => '2025-02-15',
                'max_uses' => 500,
                'used_count' => 123,
                'type' => 'event'
            ],
            'TESTCODE' => [
                'id' => 4,
                'name' => 'Test Giftcode',
                'rewards' => ['500 Coin'],
                'expires_at' => '2025-12-31',
                'max_uses' => 10,
                'used_count' => 2,
                'type' => 'public'
            ]
        ];

        // Check if giftcode exists
        if (!isset($validGiftcodes[$giftcode])) {
            return [
                'valid' => false,
                'error' => 'Mã giftcode không hợp lệ.',
                'status' => 'invalid'
            ];
        }

        $giftcodeData = $validGiftcodes[$giftcode];

        // Check if expired
        if (strtotime($giftcodeData['expires_at']) < time()) {
            return [
                'valid' => false,
                'error' => 'Giftcode đã hết hạn.',
                'status' => 'expired'
            ];
        }

        // Check if max uses reached
        if ($giftcodeData['used_count'] >= $giftcodeData['max_uses']) {
            return [
                'valid' => false,
                'error' => 'Giftcode đã hết lượt sử dụng.',
                'status' => 'used'
            ];
        }

        // Check if user already used this giftcode
        $alreadyUsed = GiftcodeUsageLog::where('user_id', $userId)
            ->where('giftcode', $giftcode)
            ->where('status', 'success')
            ->exists();

        if ($alreadyUsed) {
            return [
                'valid' => false,
                'error' => 'Bạn đã sử dụng giftcode này rồi.',
                'status' => 'used'
            ];
        }

        return [
            'valid' => true,
            'id' => $giftcodeData['id'],
            'name' => $giftcodeData['name'],
            'rewards' => $giftcodeData['rewards']
        ];
    }

    private function markGiftcodeAsUsed($giftcode, $userId)
    {
        // In real implementation, increment used_count in giftcodes table
        // For now, just log that it was marked as used
        \Log::info("Giftcode {$giftcode} marked as used by user {$userId}");
    }
}
