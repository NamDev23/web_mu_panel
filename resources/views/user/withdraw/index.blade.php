@extends('layouts.user')

@section('title', 'Rút Coin - MU Game Portal')

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
            Coin khả dụng để rút
        </div>
        <div style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
            Tổng đã nạp: {{ number_format($userAccount->getTotalRecharged()) }}đ
        </div>
    </div>
</div>

<!-- Withdraw Limits Info -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Thông tin rút coin
        </h3>
    </div>
    <div class="grid grid-3">
        <div style="text-align: center; padding: 1.5rem; background: #f0f9ff; border-radius: 8px;">
            <i class="fas fa-coins" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.25rem; font-weight: 600; color: #1e40af;">1,000 - 1,000,000</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Giới hạn mỗi lần</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f0fdf4; border-radius: 8px;">
            <i class="fas fa-calendar-day" style="font-size: 2rem; color: #10b981; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.25rem; font-weight: 600; color: #166534;">{{ number_format($stats['remaining_today']) }}</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Còn lại hôm nay</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fef3c7; border-radius: 8px;">
            <i class="fas fa-exchange-alt" style="font-size: 2rem; color: #f59e0b; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.25rem; font-weight: 600; color: #92400e;">1:1</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Tỷ lệ quy đổi</div>
        </div>
    </div>
</div>

<!-- Withdraw Form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-money-bill-transfer"></i>
            Rút coin sang tài khoản game
        </h3>
    </div>
    <div style="margin-bottom: 1.5rem;">
        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 500; color: #0369a1; margin-bottom: 0.5rem;">
                <i class="fas fa-info-circle"></i> Hướng dẫn rút coin
            </div>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Nhập chính xác tên tài khoản game nhận coin</li>
                <li>Coin sẽ được chuyển ngay lập tức vào tài khoản game</li>
                <li>Giới hạn rút: 1,000 - 1,000,000 coin mỗi lần</li>
                <li>Giới hạn hàng ngày: 500,000 coin</li>
                <li>Tỷ lệ quy đổi: 1 web coin = 1 game coin</li>
            </ul>
        </div>
    </div>

    <form method="POST" action="{{ route('user.withdraw.post') }}" id="withdrawForm">
        @csrf
        
        <div class="grid grid-2">
            <div class="form-group">
                <label for="game_username" class="form-label">Tên tài khoản game</label>
                <input 
                    type="text" 
                    name="game_username" 
                    id="game_username" 
                    class="form-input" 
                    value="{{ old('game_username') }}"
                    placeholder="Nhập tên tài khoản game nhận coin"
                    required
                    minlength="3"
                    maxlength="50"
                >
                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                    Tài khoản game phải tồn tại và đang hoạt động
                </div>
                @error('game_username')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="amount" class="form-label">Số coin muốn rút</label>
                <input 
                    type="number" 
                    name="amount" 
                    id="amount" 
                    class="form-input" 
                    value="{{ old('amount') }}"
                    placeholder="Nhập số coin"
                    required
                    min="1000"
                    max="{{ min($userAccount->getCurrentCoins(), $stats['remaining_today'], 1000000) }}"
                    step="1000"
                >
                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                    Tối thiểu: 1,000 - Tối đa: {{ number_format(min($userAccount->getCurrentCoins(), $stats['remaining_today'], 1000000)) }}
                </div>
                @error('amount')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 500; color: #92400e; margin-bottom: 0.5rem;">
                <i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng
            </div>
            <ul style="color: #92400e; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Kiểm tra kỹ tên tài khoản game trước khi rút</li>
                <li>Coin sẽ được chuyển ngay lập tức và không thể hoàn tác</li>
                <li>Chỉ rút coin cho tài khoản game của chính bạn</li>
                <li>Liên hệ admin nếu có vấn đề với giao dịch</li>
            </ul>
        </div>

        <button type="submit" class="btn btn-primary" id="withdrawBtn">
            <i class="fas fa-money-bill-transfer"></i>
            Rút coin sang game
        </button>
    </form>
</div>

<!-- Recent Withdraws -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Lịch sử rút coin gần đây
        </h3>
    </div>
    @if($recentWithdraws->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Mã GD</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Tài khoản game</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Số coin</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Trạng thái</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentWithdraws as $withdraw)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 0.75rem; font-family: monospace;">#{{ $withdraw->id }}</td>
                            <td style="padding: 0.75rem; font-weight: 500;">{{ $withdraw->game_username }}</td>
                            <td style="padding: 0.75rem; color: #ef4444; font-weight: 500;">-{{ number_format($withdraw->amount) }}</td>
                            <td style="padding: 0.75rem;">
                                <span class="status-badge {{ $withdraw->getStatusBadgeClass() }}">
                                    {{ $withdraw->getStatusIcon() }} {{ $withdraw->getStatusText() }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                                {{ $withdraw->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('user.withdraw.history') }}" class="btn btn-outline">
                Xem tất cả giao dịch
            </a>
        </div>
    @else
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>Chưa có giao dịch rút coin nào</p>
        </div>
    @endif
</div>

<!-- Statistics -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i>
            Thống kê rút coin
        </h3>
    </div>
    <div class="grid grid-4">
        <div style="text-align: center; padding: 1.5rem; background: #f0f9ff; border-radius: 8px;">
            <i class="fas fa-list" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">{{ $stats['total_withdraws'] }}</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Tổng giao dịch</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f0fdf4; border-radius: 8px;">
            <i class="fas fa-check-circle" style="font-size: 2rem; color: #10b981; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #166534;">{{ $stats['completed_withdraws'] }}</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Thành công</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fef3c7; border-radius: 8px;">
            <i class="fas fa-coins" style="font-size: 2rem; color: #f59e0b; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #92400e;">{{ number_format($stats['total_amount']) }}</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Tổng đã rút</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fdf2f8; border-radius: 8px;">
            <i class="fas fa-calendar-day" style="font-size: 2rem; color: #ec4899; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #be185d;">{{ number_format($stats['today_amount']) }}</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Hôm nay</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
// Form validation
document.getElementById('withdrawForm').addEventListener('submit', function(e) {
    const amount = parseInt(document.getElementById('amount').value);
    const gameUsername = document.getElementById('game_username').value.trim();
    
    if (!gameUsername) {
        alert('Vui lòng nhập tên tài khoản game!');
        e.preventDefault();
        return;
    }
    
    if (amount < 1000) {
        alert('Số coin tối thiểu là 1,000!');
        e.preventDefault();
        return;
    }
    
    if (amount > {{ $userAccount->getCurrentCoins() }}) {
        alert('Không đủ coin để rút!');
        e.preventDefault();
        return;
    }
    
    if (amount > {{ $stats['remaining_today'] }}) {
        alert('Vượt quá giới hạn rút coin hàng ngày!');
        e.preventDefault();
        return;
    }
    
    if (!confirm(`Bạn có chắc chắn muốn rút ${new Intl.NumberFormat().format(amount)} coin sang tài khoản game "${gameUsername}"?\n\nHành động này không thể hoàn tác!`)) {
        e.preventDefault();
        return;
    }
    
    // Disable button to prevent double submission
    const btn = document.getElementById('withdrawBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
});

// Auto format number input
document.getElementById('amount').addEventListener('input', function() {
    let value = this.value.replace(/[^0-9]/g, '');
    if (value) {
        // Round to nearest 1000
        value = Math.round(parseInt(value) / 1000) * 1000;
        this.value = value;
    }
});
@endsection
