<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class RechargeController extends Controller
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

        // Get user's coin balances
        $webCoins = $user->getWebCoins();
        if (!$webCoins) {
            // Create initial coin balance record
            DB::table('t_web_coins')->insert([
                'account_id' => $user->ID,
                'balance' => 0,
                'total_recharged' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $webCoins = (object) ['balance' => 0, 'total_recharged' => 0];
        }

        // Get game coins and characters
        $gameMoney = $user->getGameMoney();
        $gameCharacters = $user->getGameCharacters();

        // Get payment methods configuration
        $paymentMethods = $this->getPaymentMethods();

        // Get recent payment requests from t_coin_transactions
        $recentPayments = DB::table('t_coin_transactions')
            ->where('account_id', $user->ID)
            ->where('type', 'recharge')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.recharge.index', compact(
            'user',
            'webCoins',
            'gameMoney',
            'gameCharacters',
            'paymentMethods',
            'recentPayments'
        ));
    }

    public function cardRecharge(Request $request)
    {
        $request->validate([
            'card_type' => 'required|string|in:viettel,mobifone,vinaphone,vietnamobile,gmobile,zing,gate,vcoin',
            'card_amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000',
            'card_serial' => 'required|string|min:10|max:20',
            'card_code' => 'required|string|min:10|max:20',
        ]);

        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        // Calculate coins (1 VND = 1 coin)
        $coinsRequested = $request->card_amount;

        // Create transaction record
        $transactionId = DB::table('t_coin_transactions')->insertGetId([
            'account_id' => $user->ID,
            'type' => 'recharge',
            'method' => 'card',
            'amount' => $request->card_amount,
            'coins' => $coinsRequested,
            'status' => 'pending',
            'details' => json_encode([
                'card_type' => $request->card_type,
                'card_serial' => $request->card_serial,
                'card_code' => $request->card_code,
                'card_amount' => $request->card_amount
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // TODO: Integrate with card verification API
        // For now, just create pending request for admin approval

        return redirect()->back()->with(
            'success',
            "Yêu cầu nạp thẻ cào đã được gửi! Mã giao dịch: #{$transactionId}. " .
                "Chúng tôi sẽ xử lý trong vòng 5-10 phút."
        );
    }

    public function bankTransfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000|max:10000000',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        // Calculate coins (1 VND = 1 coin)
        $coinsRequested = $request->amount;

        // Bank info from .env config (QR code is static)
        $qrData = [
            'bank_name' => env('BANK_NAME', 'Ngân hàng VP BANK'),
            'account_number' => env('BANK_ACCOUNT_NUMBER', '0862968396'),
            'account_name' => env('BANK_ACCOUNT_NAME', 'DINH THI DIEU LINH'),
            'amount' => $request->amount,
            'content' => "NAPTHE {$user->ID} " . time(),
            'qr_image' => env('BANK_QR_IMAGE', 'images/qr_vp.jpg')
        ];

        // Handle proof image upload
        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $proofImagePath = $request->file('proof_image')->store('payment_proofs', 'public');
        }

        // Get current balance
        $currentBalance = $user->getWebCoins();
        $balanceBefore = $currentBalance ? $currentBalance->balance : 0;
        $balanceAfter = $balanceBefore; // Will be updated when admin approves

        // Create transaction record
        $transactionId = DB::table('t_coin_transactions')->insertGetId([
            'account_id' => $user->ID,
            'type' => 'recharge',
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => "Nạp coin qua chuyển khoản ngân hàng - {$request->amount}đ",
            'reference_type' => 'bank_transfer',
            'reference_id' => $user->ID,
            'processed_by' => null, // Will be set when admin processes
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Store additional details in a separate table or use description field
        // For now, we'll store bank info in description
        DB::table('t_coin_transactions')
            ->where('id', $transactionId)
            ->update([
                'description' => json_encode([
                    'type' => 'bank_transfer',
                    'amount' => $request->amount,
                    'coins_requested' => $coinsRequested,
                    'bank_info' => $qrData,
                    'proof_image' => $proofImagePath,
                    'transaction_ref' => "BANK_{$user->ID}_" . time(),
                    'status' => 'pending'
                ])
            ]);

        return redirect()->back()->with(
            'success',
            "Yêu cầu chuyển khoản đã được tạo! Mã giao dịch: #{$transactionId}. " .
                "Vui lòng chuyển khoản theo thông tin được cung cấp."
        )->with('qr_data', $qrData);
    }

    public function history(Request $request)
    {
        $user = Session::get('user_account');

        $query = UserPaymentRequest::where('user_id', $user['id']);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('method') && !empty($request->method)) {
            $query->where('payment_method', $request->method);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('user.recharge.history', compact('payments'));
    }

    public function show($id)
    {
        $user = Session::get('user_account');

        $payment = UserPaymentRequest::where('user_id', $user['id'])
            ->where('id', $id)
            ->firstOrFail();

        return view('user.recharge.show', compact('payment'));
    }

    private function getPaymentMethods()
    {
        return [
            'card' => [
                'name' => 'Thẻ cào điện thoại',
                'icon' => '💳',
                'description' => 'Nạp bằng thẻ cào Viettel, Mobifone, Vinaphone...',
                'processing_time' => '5-10 phút',
                'enabled' => true
            ],
            'bank_transfer' => [
                'name' => 'Chuyển khoản ngân hàng',
                'icon' => '🏦',
                'description' => 'Chuyển khoản qua QR code hoặc số tài khoản',
                'processing_time' => '10-30 phút',
                'enabled' => true
            ],
            'paypal' => [
                'name' => 'PayPal',
                'icon' => '💰',
                'description' => 'Thanh toán quốc tế qua PayPal',
                'processing_time' => '5-15 phút',
                'enabled' => false // Disabled for now
            ]
        ];
    }
}
