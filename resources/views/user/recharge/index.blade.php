@extends('layouts.user')

@section('title', 'Nạp Coin - MU Game Portal')

@section('content')
<!-- Current Balance -->
<div class="grid grid-2">
    <!-- Web Coins -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-wallet" style="color: #3b82f6;"></i>
                Coin Website
            </h3>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2.5rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.5rem;">
                {{ number_format($webCoins->balance ?? 0) }}
            </div>
            <div style="color: #6b7280; font-size: 1rem;">
                Coin khả dụng trên web
            </div>
            <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
                Tổng đã nạp: {{ number_format($webCoins->total_recharged ?? 0) }}đ
            </div>
        </div>
    </div>

    <!-- Game Coins -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-gamepad" style="color: #10b981;"></i>
                Coin Game ({{ $user->getGameUserId() }})
            </h3>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2.5rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem;">
                {{ number_format($gameMoney->money ?? 0) }}
            </div>
            <div style="color: #6b7280; font-size: 1rem;">
                Coin trong game
            </div>
            <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
                Nhân vật: {{ $gameCharacters->count() }} characters
            </div>
        </div>
    </div>
</div>

<!-- Game Characters -->
@if($gameCharacters->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users" style="color: #8b5cf6;"></i>
            Nhân vật trong game
        </h3>
    </div>
    <div class="grid grid-4">
        @foreach($gameCharacters as $character)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; text-align: center;">
                <div style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    {{ $character->rname }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">
                    ID: {{ $character->rid }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Payment Methods -->
{{--
<!-- Card Recharge - COMMENTED OUT -->
<div class="grid grid-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-credit-card"></i>
                Nạp thẻ cào
            </h3>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                <div style="font-weight: 500; color: #0369a1; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Thông tin nạp thẻ
                </div>
                <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                    <li>Hỗ trợ: Viettel, Mobifone, Vinaphone, Vietnamobile</li>
                    <li>Tỷ lệ: 1,000đ = 100 Coin</li>
                    <li>Thời gian xử lý: 5-10 phút</li>
                    <li>Phí: Miễn phí</li>
                </ul>
            </div>
        </div>

        <form method="POST" action="{{ route('user.recharge.card') }}" id="cardForm">
            @csrf

            <div class="form-group">
                <label for="card_type" class="form-label">Loại thẻ</label>
                <select name="card_type" id="card_type" class="form-select" required>
                    <option value="">Chọn loại thẻ</option>
                    <option value="viettel" {{ old('card_type') == 'viettel' ? 'selected' : '' }}>Viettel</option>
                    <option value="mobifone" {{ old('card_type') == 'mobifone' ? 'selected' : '' }}>Mobifone</option>
                    <option value="vinaphone" {{ old('card_type') == 'vinaphone' ? 'selected' : '' }}>Vinaphone</option>
                    <option value="vietnamobile" {{ old('card_type') == 'vietnamobile' ? 'selected' : '' }}>Vietnamobile</option>
                    <option value="gmobile" {{ old('card_type') == 'gmobile' ? 'selected' : '' }}>Gmobile</option>
                </select>
                @error('card_type')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="card_amount" class="form-label">Mệnh giá</label>
                <select name="card_amount" id="card_amount" class="form-select" required>
                    <option value="">Chọn mệnh giá</option>
                    <option value="10000" {{ old('card_amount') == '10000' ? 'selected' : '' }}>10,000đ (10,000 Coin)</option>
                    <option value="20000" {{ old('card_amount') == '20000' ? 'selected' : '' }}>20,000đ (20,000 Coin)</option>
                    <option value="30000" {{ old('card_amount') == '30000' ? 'selected' : '' }}>30,000đ (30,000 Coin)</option>
                    <option value="50000" {{ old('card_amount') == '50000' ? 'selected' : '' }}>50,000đ (50,000 Coin)</option>
                    <option value="100000" {{ old('card_amount') == '100000' ? 'selected' : '' }}>100,000đ (100,000 Coin)</option>
                    <option value="200000" {{ old('card_amount') == '200000' ? 'selected' : '' }}>200,000đ (200,000 Coin)</option>
                    <option value="300000" {{ old('card_amount') == '300000' ? 'selected' : '' }}>300,000đ (300,000 Coin)</option>
                    <option value="500000" {{ old('card_amount') == '500000' ? 'selected' : '' }}>500,000đ (500,000 Coin)</option>
                    <option value="1000000" {{ old('card_amount') == '1000000' ? 'selected' : '' }}>1,000,000đ (1,000,000 Coin)</option>
                </select>
                @error('card_amount')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="card_serial" class="form-label">Số serial</label>
                <input
                    type="text"
                    name="card_serial"
                    id="card_serial"
                    class="form-input"
                    value="{{ old('card_serial') }}"
                    placeholder="Nhập số serial trên thẻ"
                    required
                    minlength="10"
                    maxlength="20"
                >
                @error('card_serial')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="card_code" class="form-label">Mã thẻ</label>
                <input
                    type="text"
                    name="card_code"
                    id="card_code"
                    class="form-input"
                    value="{{ old('card_code') }}"
                    placeholder="Nhập mã thẻ (cào lớp bạc)"
                    required
                    minlength="10"
                    maxlength="20"
                >
                @error('card_code')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-credit-card"></i>
                Nạp thẻ cào
            </button>
        </form>
    </div>
--}}

<!-- Bank Transfer Only -->
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-university"></i>
            Chuyển khoản ngân hàng
        </h3>
    </div>
    <div style="margin-bottom: 1.5rem;">
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
            <div style="font-weight: 500; color: #166534; margin-bottom: 0.5rem;">
                <i class="fas fa-info-circle"></i> Thông tin chuyển khoản
            </div>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Ngân hàng: {{ env('BANK_NAME', 'VP Bank') }}</li>
                <li>Tỷ lệ: 1,000đ = 1,000 Coin</li>
                <li>Thời gian xử lý: 10-30 phút</li>
                <li>Phí: Miễn phí</li>
            </ul>
        </div>
    </div>

    <form method="POST" action="{{ route('user.recharge.bank') }}" id="bankForm" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="amount" class="form-label">Số tiền (VND)</label>
            <input
                type="number"
                name="amount"
                id="amount"
                class="form-input"
                value="{{ old('amount') }}"
                placeholder="Nhập số tiền muốn nạp"
                required
                min="10000"
                max="10000000"
                step="1000"
            >
            <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                Tối thiểu: 10,000đ - Tối đa: 10,000,000đ
            </div>
            <div id="coinPreview" style="color: #10b981; font-size: 0.875rem; font-weight: 500; margin-top: 0.25rem;"></div>
            @error('amount')
                <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="proof_image" class="form-label">Ảnh chứng minh chuyển khoản (Tùy chọn)</label>
            <input
                type="file"
                name="proof_image"
                id="proof_image"
                class="form-input"
                accept="image/jpeg,image/png,image/jpg"
            >
            <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                Tải lên ảnh chụp màn hình giao dịch để xử lý nhanh hơn
            </div>
            @error('proof_image')
                <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-paper-plane"></i>
            Gửi yêu cầu chuyển khoản
        </button>
    </form>
</div>

<!-- QR Code Display (if generated) -->
@if(session('qr_data'))
    <div class="card" style="border: 2px solid #10b981;">
        <div class="card-header">
            <h3 class="card-title" style="color: #10b981;">
                <i class="fas fa-qrcode"></i>
                Thông tin chuyển khoản
            </h3>
        </div>
        <div class="grid grid-2">
            <div>
                <h4 style="margin-bottom: 1rem; color: #374151;">Thông tin ngân hàng:</h4>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px;">
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Ngân hàng:</strong> {{ session('qr_data.bank_name') }}
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Số tài khoản:</strong> {{ session('qr_data.account_number') }}
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Tên tài khoản:</strong> {{ session('qr_data.account_name') }}
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Số tiền:</strong> {{ number_format(session('qr_data.amount')) }}đ
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Nội dung:</strong>
                        <code style="background: #e5e7eb; padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace;">
                            {{ session('qr_data.content') }}
                        </code>
                    </div>
                </div>
                <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <div style="font-weight: 500; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:
                    </div>
                    <ul style="color: #92400e; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                        <li>Chuyển khoản đúng số tiền và nội dung</li>
                        <li>Không thay đổi nội dung chuyển khoản</li>
                        <li>Giao dịch sẽ được xử lý trong 10-30 phút</li>
                        <li>Liên hệ admin nếu quá 1 giờ chưa nhận được coin</li>
                    </ul>
                </div>
            </div>
            <div style="text-align: center;">
                <h4 style="margin-bottom: 1rem; color: #374151;">Quét mã QR:</h4>
                <div style="background: white; padding: 1rem; border-radius: 8px; display: inline-block;">
                    <img src="{{ asset(session('qr_data.qr_image', 'images/qr_vp.jpg')) }}" alt="QR Code" style="width: 200px; height: 200px;" />
                </div>
                <div style="margin-top: 1rem;">
                    <button onclick="copyBankInfo()" class="btn btn-outline">
                        <i class="fas fa-copy"></i>
                        Sao chép thông tin
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Giao dịch gần đây
        </h3>
    </div>
    @if($recentPayments->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Mã GD</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Phương thức</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Số tiền</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Coin</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Trạng thái</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPayments as $payment)
                        @php
                            // Try to decode description as JSON, fallback to plain text
                            $details = json_decode($payment->description, true);
                            if ($details) {
                                $method = $details['type'] ?? $payment->reference_type;
                                $coinsRequested = $details['coins_requested'] ?? $payment->amount;
                                $status = $details['status'] ?? 'pending';
                            } else {
                                $method = $payment->reference_type;
                                $coinsRequested = $payment->amount;
                                $status = $payment->processed_by ? 'completed' : 'pending';
                            }

                            $methodIcon = $method == 'bank_transfer' ? '🏦' : '💳';
                            $methodText = $method == 'bank_transfer' ? 'Chuyển khoản' : 'Thẻ cào';

                            $statusClass = '';
                            $statusText = '';
                            switch($status) {
                                case 'pending':
                                    $statusClass = 'status-pending';
                                    $statusText = 'Đang xử lý';
                                    break;
                                case 'completed':
                                    $statusClass = 'status-success';
                                    $statusText = 'Thành công';
                                    break;
                                case 'failed':
                                    $statusClass = 'status-failed';
                                    $statusText = 'Thất bại';
                                    break;
                                default:
                                    $statusClass = 'status-pending';
                                    $statusText = 'Chưa xác định';
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 0.75rem; font-family: monospace;">#{{ $payment->id }}</td>
                            <td style="padding: 0.75rem;">
                                {{ $methodIcon }} {{ $methodText }}
                            </td>
                            <td style="padding: 0.75rem; font-weight: 500;">{{ number_format($payment->amount) }}đ</td>
                            <td style="padding: 0.75rem; color: #f59e0b; font-weight: 500;">{{ number_format($coinsRequested) }}</td>
                            <td style="padding: 0.75rem;">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('user.recharge.history') }}" class="btn btn-outline">
                Xem tất cả giao dịch
            </a>
        </div>
    @else
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>Chưa có giao dịch nào</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
// Calculate coin preview for bank transfer
document.getElementById('amount').addEventListener('input', function() {
    const amount = parseInt(this.value) || 0;
    const coins = amount; // 1 VND = 1 coin
    const preview = document.getElementById('coinPreview');

    if (amount >= 10000) {
        preview.textContent = `≈ ${new Intl.NumberFormat().format(coins)} Coin`;
    } else {
        preview.textContent = '';
    }
});

// Copy bank info function
function copyBankInfo() {
    const bankInfo = `Ngân hàng: {{ env('BANK_NAME', 'Ngân hàng VP BANK') }}
Số tài khoản: {{ env('BANK_ACCOUNT_NUMBER', '0862968396') }}
Tên tài khoản: {{ env('BANK_ACCOUNT_NAME', 'DINH THI DIEU LINH') }}
Nội dung: NAPTHE {{ $user->ID }}`;

    navigator.clipboard.writeText(bankInfo).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
        btn.style.background = '#10b981';
        btn.style.color = 'white';

        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    }).catch(function(err) {
        alert('Không thể sao chép. Vui lòng sao chép thủ công.');
    });
}
@endsection
