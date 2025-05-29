@extends('layouts.user')

@section('title', 'Dashboard - MU Game Portal')

@section('content')
<div class="grid grid-4">
    <!-- Coin Balance Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-coins text-yellow-500"></i>
                Số dư Coin
            </h3>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
                {{ number_format($stats['web_coins']) }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">
                Tổng nạp: {{ number_format($stats['total_recharged']) }}đ
            </div>
            <a href="{{ route('user.recharge') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i> Nạp thêm
            </a>
        </div>
    </div>

    <!-- Payment Stats Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line text-blue-500"></i>
                Thống kê giao dịch
            </h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #6b7280;">Tổng giao dịch:</span>
                <span style="font-weight: 600;">{{ $stats['total_payments'] }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #6b7280;">Hoàn thành:</span>
                <span style="font-weight: 600; color: #10b981;">{{ $stats['completed_payments'] }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #6b7280;">Đang chờ:</span>
                <span style="font-weight: 600; color: #f59e0b;">{{ $stats['pending_payments'] }}</span>
            </div>
        </div>
    </div>

    <!-- Monthly Cards Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt text-purple-500"></i>
                Thẻ tháng
            </h3>
        </div>
        <div style="text-align: center;">
            @if($userAccount->game_account_id)
                <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6; margin-bottom: 0.5rem;">
                    {{ $stats['active_monthly_cards'] }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">
                    Thẻ đang hoạt động
                </div>
                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                    Tổng: {{ $stats['total_monthly_cards'] }} thẻ
                </div>
            @else
                <div style="color: #6b7280; font-size: 0.875rem;">
                    Chưa liên kết tài khoản game
                </div>
                <a href="{{ route('user.profile') }}" class="btn btn-outline" style="margin-top: 1rem; font-size: 0.75rem;">
                    Liên kết ngay
                </a>
            @endif
        </div>
    </div>

    <!-- Battle Pass Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-trophy text-orange-500"></i>
                Battle Pass
            </h3>
        </div>
        <div style="text-align: center;">
            @if($battlePassProgress)
                <div style="font-size: 1.5rem; font-weight: 700; color: #f97316; margin-bottom: 0.5rem;">
                    Level {{ $battlePassProgress->current_level }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">
                    {{ $battlePassProgress->has_premium ? 'Premium' : 'Free' }}
                </div>
                <div style="background: #f3f4f6; border-radius: 9999px; height: 0.5rem; margin: 1rem 0;">
                    <div style="background: #f97316; height: 100%; border-radius: 9999px; width: {{ $battlePassProgress->getProgressPercentage() }}%;"></div>
                </div>
            @elseif($userAccount->game_account_id)
                <div style="color: #6b7280; font-size: 0.875rem;">
                    Chưa tham gia Battle Pass
                </div>
            @else
                <div style="color: #6b7280; font-size: 0.875rem;">
                    Chưa liên kết tài khoản game
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Thao tác nhanh
        </h3>
    </div>
    <div class="grid grid-4">
        <a href="{{ route('user.recharge') }}" class="btn btn-primary">
            <i class="fas fa-coins"></i>
            Nạp Coin
        </a>
        <a href="{{ route('user.withdraw') }}" class="btn btn-success">
            <i class="fas fa-money-bill-transfer"></i>
            Rút Coin
        </a>
        <a href="{{ route('user.giftcode') }}" class="btn btn-warning">
            <i class="fas fa-gift"></i>
            Nhập Giftcode
        </a>
        <a href="{{ route('user.recharge.history') }}" class="btn btn-outline">
            <i class="fas fa-history"></i>
            Lịch sử giao dịch
        </a>
    </div>
</div>

<!-- Recent Activities -->
<div class="grid grid-2">
    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clock"></i>
                Giao dịch gần đây
            </h3>
        </div>
        @if($recentPayments->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($recentPayments as $payment)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 8px;">
                        <div>
                            <div style="font-weight: 500;">
                                {{ $payment->getPaymentMethodIcon() }} {{ $payment->getPaymentMethodText() }}
                            </div>
                            <div style="font-size: 0.75rem; color: #6b7280;">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600;">{{ number_format($payment->amount) }}đ</div>
                            <span class="status-badge {{ $payment->getStatusBadgeClass() }}">
                                {{ $payment->getStatusText() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('user.recharge.history') }}" class="btn btn-outline">
                    Xem tất cả
                </a>
            </div>
        @else
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Chưa có giao dịch nào</p>
                <a href="{{ route('user.recharge') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Nạp coin đầu tiên
                </a>
            </div>
        @endif
    </div>

    <!-- Monthly Cards -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-check"></i>
                Thẻ tháng đang hoạt động
            </h3>
        </div>
        @if($monthlyCards->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($monthlyCards as $card)
                    <div style="padding: 0.75rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                        <div style="font-weight: 500; color: #0369a1;">
                            {{ $card->package_name }}
                        </div>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                            Hết hạn: {{ date('d/m/Y', strtotime($card->expires_at)) }}
                        </div>
                        <div style="font-size: 0.75rem; color: #059669; margin-top: 0.25rem;">
                            {{ $card->duration_days }} ngày - {{ number_format($card->price) }}đ
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Không có thẻ tháng nào đang hoạt động</p>
                @if(!$userAccount->game_account_id)
                    <p style="font-size: 0.875rem; margin-top: 0.5rem;">
                        Liên kết tài khoản game để xem thông tin thẻ tháng
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Game Account Info -->
@if($userAccount->game_account_id && $userAccount->gameAccount)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-gamepad"></i>
                Thông tin tài khoản game
            </h3>
        </div>
        <div class="grid grid-3">
            <div style="text-align: center;">
                <div style="font-size: 1.25rem; font-weight: 600; color: #667eea;">
                    {{ $userAccount->gameAccount->username }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">Tên tài khoản</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.25rem; font-weight: 600; color: #10b981;">
                    {{ number_format($userAccount->gameAccount->current_balance ?? 0) }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">Coin trong game</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.25rem; font-weight: 600; color: #f59e0b;">
                    {{ date('d/m/Y', strtotime($userAccount->gameAccount->created_at ?? now())) }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">Ngày tạo</div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
// Auto refresh stats every 30 seconds
setInterval(function() {
    fetch('{{ route("user.api.quick-stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update coin balance display
                const coinElement = document.querySelector('[data-coin-balance]');
                if (coinElement) {
                    coinElement.textContent = new Intl.NumberFormat().format(data.stats.web_coins);
                }

                // Update pending payments count
                const pendingElement = document.querySelector('[data-pending-payments]');
                if (pendingElement) {
                    pendingElement.textContent = data.stats.pending_payments;
                }
            }
        })
        .catch(error => console.log('Stats refresh error:', error));
}, 30000);
@endsection
