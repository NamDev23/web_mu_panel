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

        // Get user's coin balances from user_coins table
        $userCoins = DB::table('user_coins')->where('account_id', $user->ID)->first();
        if (!$userCoins) {
            // Create initial coin balance record
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

        // Get game coins and characters
        $gameMoney = $user->getGameMoney();
        $gameCharacters = $user->getGameCharacters();

        // Get payment methods configuration
        $paymentMethods = $this->getPaymentMethods();

        // Get recent payment requests from coin_recharge_logs
        $recentPayments = DB::table('coin_recharge_logs')
            ->where('account_id', $user->ID)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.recharge.index', compact(
            'user',
            'userCoins',
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

        // Generate transaction ID
        $transactionId = 'CARD_' . time() . '_' . rand(1000, 9999);

        // Create transaction record in coin_recharge_logs
        $rechargeId = DB::table('coin_recharge_logs')->insertGetId([
            'account_id' => $user->ID,
            'username' => $user->UserName,
            'transaction_id' => $transactionId,
            'amount_vnd' => $request->card_amount,
            'coins_added' => $coinsRequested,
            'type' => 'card',
            'status' => 'pending',
            'note' => "Nạp thẻ cào {$request->card_type} - {$request->card_amount}đ",
            'payment_method' => $request->card_type,
            'payment_data' => json_encode([
                'card_type' => $request->card_type,
                'card_serial' => $request->card_serial,
                'card_code' => $request->card_code,
                'card_amount' => $request->card_amount
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // TODO: Integrate with card verification API
        // For now, just create pending request for admin approval

        return redirect()->back()->with(
            'success',
            "Yêu cầu nạp thẻ cào đã được gửi! Mã giao dịch: {$transactionId}. " .
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

        // Generate transaction ID
        $transactionId = 'BANK_' . time() . '_' . rand(1000, 9999);

        // Create transaction record in coin_recharge_logs
        $rechargeId = DB::table('coin_recharge_logs')->insertGetId([
            'account_id' => $user->ID,
            'username' => $user->UserName,
            'transaction_id' => $transactionId,
            'amount_vnd' => $request->amount,
            'coins_added' => $coinsRequested,
            'type' => 'bank',
            'status' => 'pending',
            'note' => "Nạp coin qua chuyển khoản ngân hàng - {$request->amount}đ",
            'payment_method' => 'bank_transfer',
            'payment_data' => json_encode([
                'type' => 'bank_transfer',
                'amount' => $request->amount,
                'coins_requested' => $coinsRequested,
                'bank_info' => $qrData,
                'proof_image' => $proofImagePath,
                'transaction_ref' => "BANK_{$user->ID}_" . time()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with(
            'success',
            "Yêu cầu chuyển khoản đã được tạo! Mã giao dịch: {$transactionId}. " .
                "Vui lòng chuyển khoản theo thông tin được cung cấp."
        )->with('qr_data', $qrData);
    }

    public function history(Request $request)
    {
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        $query = DB::table('coin_recharge_logs')->where('account_id', $user->ID);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('method') && !empty($request->method)) {
            $query->where('type', $request->method);
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
        $userSession = Session::get('user_account');
        $user = Account::find($userSession['id']);

        $payment = DB::table('coin_recharge_logs')
            ->where('account_id', $user->ID)
            ->where('id', $id)
            ->first();

        if (!$payment) {
            abort(404);
        }

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
