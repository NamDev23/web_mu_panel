@extends('layouts.user')

@section('title', 'Dashboard - MU Game Portal')

@section('content')
<!-- Welcome Message -->
<div class="card">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">
        🎮 Chào mừng, {{ $stats['username'] }}!
    </h1>
    <p style="color: #6b7280; font-size: 1.1rem;">
        Chào mừng bạn đến với MU Game Portal. Quản lý tài khoản và giao dịch của bạn tại đây.
    </p>
</div>

<!-- Account Info Cards -->
<div class="grid grid-4">
    <!-- Current Coins -->
    <div class="card" style="text-align: center;">
        <div style="background: #fef3c7; color: #d97706; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-coins" style="font-size: 1.5rem;"></i>
        </div>
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Coin hiện tại</h3>
        <p style="color: #d97706; font-weight: 700; font-size: 1.2rem;">{{ number_format($stats['coins']) }}</p>
    </div>

    <!-- Account Status -->
    <div class="card" style="text-align: center;">
        <div style="background: #dcfce7; color: #166534; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-user-check" style="font-size: 1.5rem;"></i>
        </div>
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Trạng thái</h3>
        <p style="color: #059669; font-weight: 600;">{{ $stats['status'] }}</p>
    </div>

    <!-- Total Recharged -->
    <div class="card" style="text-align: center;">
        <div style="background: #dbeafe; color: #1e40af; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-chart-line" style="font-size: 1.5rem;"></i>
        </div>
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Tổng nạp</h3>
        <p style="color: #1e40af; font-weight: 600;">{{ number_format($stats['total_recharged']) }} VNĐ</p>
    </div>

    <!-- Last Login -->
    <div class="card" style="text-align: center;">
        <div style="background: #fed7aa; color: #ea580c; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-clock" style="font-size: 1.5rem;"></i>
        </div>
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Đăng nhập cuối</h3>
        <p style="color: #6b7280; font-size: 0.9rem;">{{ $stats['last_login']->format('d/m/Y H:i') }}</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-bolt" style="color: #f59e0b;"></i>
            Thao tác nhanh
        </h2>
    </div>
    <div class="grid grid-4">
        <a href="{{ route('user.recharge') }}" class="btn btn-primary" style="text-align: center; display: flex; flex-direction: column; gap: 0.5rem;">
            <i class="fas fa-coins" style="font-size: 1.5rem;"></i>
            Nạp Coin
        </a>
        <a href="#" class="btn btn-success" style="text-align: center; display: flex; flex-direction: column; gap: 0.5rem;">
            <i class="fas fa-money-bill-transfer" style="font-size: 1.5rem;"></i>
            Rút Coin
        </a>
        <a href="#" class="btn btn-warning" style="text-align: center; display: flex; flex-direction: column; gap: 0.5rem;">
            <i class="fas fa-gift" style="font-size: 1.5rem;"></i>
            Nhập Giftcode
        </a>
        <a href="#" class="btn btn-outline" style="text-align: center; display: flex; flex-direction: column; gap: 0.5rem;">
            <i class="fas fa-history" style="font-size: 1.5rem;"></i>
            Lịch sử
        </a>
    </div>
</div>

<!-- Coin Information -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-coins" style="color: #f59e0b;"></i>
            Thông tin Coin
        </h2>
    </div>
    <div class="grid grid-3">
        <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #92400e; margin-bottom: 0.5rem;">Coin hiện tại</h3>
            <p style="font-size: 1.5rem; font-weight: 700; color: #d97706;">{{ number_format($stats['coins']) }}</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 12px;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📈</div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e40af; margin-bottom: 0.5rem;">Tổng đã nạp</h3>
            <p style="font-size: 1.2rem; font-weight: 600; color: #2563eb;">{{ number_format($stats['total_recharged']) }} VNĐ</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border-radius: 12px;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💸</div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #be185d; margin-bottom: 0.5rem;">Tổng đã tiêu</h3>
            <p style="font-size: 1.2rem; font-weight: 600; color: #db2777;">{{ number_format($stats['total_spent']) }} Coin</p>
        </div>
    </div>
</div>

<!-- Account Details -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-user" style="color: #667eea;"></i>
            Thông tin tài khoản chi tiết
        </h2>
    </div>
    <div class="grid grid-2">
        <div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">Thông tin cơ bản</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">Tên đăng nhập:</span>
                    <span style="font-weight: 600;">{{ $user->UserName }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">Email:</span>
                    <span style="font-weight: 600;">{{ $user->Email ?: 'Chưa cập nhật' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                    <span style="color: #6b7280;">Trạng thái:</span>
                    <span class="status-badge status-active">{{ $user->getStatusText() }}</span>
                </div>
            </div>
        </div>
        <div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">Thời gian</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                    <span style="color: #6b7280;">Ngày tạo:</span>
                    <span style="font-weight: 600;">{{ $user->CreateTime->format('d/m/Y H:i') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                    <span style="color: #6b7280;">Đăng nhập cuối:</span>
                    <span style="font-weight: 600;">{{ $user->LastLoginTime->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
