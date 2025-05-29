@extends('layouts.user')

@section('title', 'Nạp Coin - MU Game Portal')

@section('content')
<!-- Current Balance -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-wallet"></i>
            Số dư hiện tại
        </h3>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 3rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
            {{ number_format($userAccount->getCurrentCoins()) }}
        </div>
        <div style="color: #6b7280; font-size: 1rem;">
            Coin khả dụng
        </div>
        <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
            Tổng đã nạp: {{ number_format($userAccount->getTotalRecharged()) }}đ
        </div>
    </div>
</div>

<!-- Payment Methods -->
<div class="grid grid-2">
    <!-- Card Recharge -->
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
                    @foreach(\App\Models\UserPaymentRequest::getCardTypes() as $key => $name)
                        <option value="{{ $key }}" {{ old('card_type') == $key ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('card_type')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="card_amount" class="form-label">Mệnh giá</label>
                <select name="card_amount" id="card_amount" class="form-select" required>
                    <option value="">Chọn mệnh giá</option>
                    @foreach(\App\Models\UserPaymentRequest::getCardDenominations() as $value => $label)
                        <option value="{{ $value }}" {{ old('card_amount') == $value ? 'selected' : '' }}>
                            {{ $label }} ({{ number_format(\App\Models\UserPaymentRequest::calculateCoins($value)) }} Coin)
                        </option>
                    @endforeach
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

    <!-- Bank Transfer -->
    <div class="card">
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
                    <li>Ngân hàng: Vietcombank</li>
                    <li>Tỷ lệ: 1,000đ = 100 Coin</li>
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
                <i class="fas fa-qrcode"></i>
                Tạo QR chuyển khoản
            </button>
        </form>
    </div>
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
                    <div id="qrcode" style="width: 200px; height: 200px;"></div>
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
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 0.75rem; font-family: monospace;">#{{ $payment->id }}</td>
                            <td style="padding: 0.75rem;">
                                {{ $payment->getPaymentMethodIcon() }} {{ $payment->getPaymentMethodText() }}
                            </td>
                            <td style="padding: 0.75rem; font-weight: 500;">{{ number_format($payment->amount) }}đ</td>
                            <td style="padding: 0.75rem; color: #f59e0b; font-weight: 500;">{{ number_format($payment->coins_requested) }}</td>
                            <td style="padding: 0.75rem;">
                                <span class="status-badge {{ $payment->getStatusBadgeClass() }}">
                                    {{ $payment->getStatusText() }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
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
    const coins = Math.floor(amount * 0.1); // 1000 VND = 100 coins
    const preview = document.getElementById('coinPreview');
    
    if (amount >= 10000) {
        preview.textContent = `≈ ${new Intl.NumberFormat().format(coins)} Coin`;
    } else {
        preview.textContent = '';
    }
});

// Copy bank info function
function copyBankInfo() {
    const bankInfo = `Ngân hàng: {{ session('qr_data.bank_name', '') }}
Số tài khoản: {{ session('qr_data.account_number', '') }}
Tên tài khoản: {{ session('qr_data.account_name', '') }}
Số tiền: {{ number_format(session('qr_data.amount', 0)) }}đ
Nội dung: {{ session('qr_data.content', '') }}`;
    
    navigator.clipboard.writeText(bankInfo).then(function() {
        alert('Đã sao chép thông tin chuyển khoản!');
    });
}

@if(session('qr_data'))
// Generate QR Code
const qrData = '{{ session("qr_data.qr_string") }}';
// You would need to include a QR code library like qrcode.js
// For now, just show a placeholder
document.getElementById('qrcode').innerHTML = '<div style="background: #f3f4f6; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #6b7280;">QR Code<br>Placeholder</div>';
@endif
@endsection
