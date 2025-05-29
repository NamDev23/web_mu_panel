<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\UserAccount;
use App\Models\UserPaymentRequest;

class RechargeController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        // Get payment methods configuration
        $paymentMethods = $this->getPaymentMethods();

        // Get recent payment requests
        $recentPayments = UserPaymentRequest::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.recharge.index', compact(
            'userAccount',
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

        $user = Session::get('user_account');
        $coinsRequested = UserPaymentRequest::calculateCoins($request->card_amount);

        // Create payment request
        $paymentRequest = UserPaymentRequest::create([
            'user_id' => $user['id'],
            'payment_method' => 'card',
            'amount' => $request->card_amount,
            'coins_requested' => $coinsRequested,
            'status' => 'pending',
            'card_details' => [
                'type' => $request->card_type,
                'serial' => $request->card_serial,
                'code' => $request->card_code,
                'amount' => $request->card_amount
            ]
        ]);

        // TODO: Integrate with card verification API
        // For now, just create pending request for admin approval

        return redirect()->back()->with('success', 
            "Yêu cầu nạp thẻ cào đã được gửi! Mã giao dịch: #{$paymentRequest->id}. " .
            "Chúng tôi sẽ xử lý trong vòng 5-10 phút."
        );
    }

    public function bankTransfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000|max:10000000',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Session::get('user_account');
        $coinsRequested = UserPaymentRequest::calculateCoins($request->amount);

        // Generate QR code data
        $qrData = UserPaymentRequest::generateBankQRData(
            $request->amount, 
            $user['id'], 
            time()
        );

        // Handle proof image upload
        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $proofImagePath = $request->file('proof_image')->store('payment_proofs', 'public');
        }

        // Create payment request
        $paymentRequest = UserPaymentRequest::create([
            'user_id' => $user['id'],
            'payment_method' => 'bank_transfer',
            'amount' => $request->amount,
            'coins_requested' => $coinsRequested,
            'status' => 'pending',
            'qr_code_data' => $qrData,
            'proof_image' => $proofImagePath,
            'transaction_ref' => "BANK_{$user['id']}_" . time()
        ]);

        return redirect()->back()->with('success', 
            "Yêu cầu chuyển khoản đã được tạo! Mã giao dịch: #{$paymentRequest->id}. " .
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
