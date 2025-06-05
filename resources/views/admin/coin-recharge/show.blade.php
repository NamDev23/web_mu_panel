@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản {{ $account->UserName }} - MU Admin Panel')

@section('styles')
<style>
        .breadcrumb {
            color: white;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        .breadcrumb a {
            color: white;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .transaction-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .transaction-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .transaction-meta {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .type-badge {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .amount-badge {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 500;
            opacity: 0.8;
        }
        .info-value {
            font-weight: 600;
        }
        .amount-text {
            color: #10b981;
            font-weight: 700;
        }
        .coins-text {
            color: #f59e0b;
            font-weight: 700;
        }
        .transaction-id {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .note-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
        .note-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #3b82f6;
        }
        .note-content {
            opacity: 0.9;
            line-height: 1.5;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-secondary {
            background: rgba(107, 114, 128, 0.8);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .timeline {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
        }
        .timeline-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .timeline-item:last-child {
            border-bottom: none;
        }
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3b82f6, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .timeline-content {
            flex: 1;
        }
        .timeline-action {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .timeline-time {
            font-size: 12px;
            opacity: 0.7;
        }
        .success-message {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/admin/dashboard">Dashboard</a> /
            <a href="/admin/coin-recharge">Quản lý xu game</a> /
            {{ $account->UserName }}
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Account Header -->
        <div class="transaction-header">
            <div class="transaction-info">
                <h1>👤 Tài khoản {{ $account->UserName }}</h1>
                <div class="transaction-meta">
                    <span class="status-badge {{ $account->Status == 1 ? 'status-completed' : 'status-failed' }}">
                        {{ $account->Status == 1 ? '✅ Hoạt động' : '❌ Bị khóa' }}
                    </span>
                    <span class="type-badge">ID: {{ $account->ID }}</span>
                    <span class="amount-badge">{{ number_format($money->YuanBao) }} YB</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.coin-recharge.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
                <a href="{{ route('admin.game-money.edit', $account->ID) }}" class="btn btn-primary">✏️ Sửa xu</a>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Money Details -->
            <div class="info-card">
                <h3 class="card-title">💰 Chi tiết xu game</h3>
                <div class="info-row">
                    <span class="info-label">Game User ID:</span>
                    <span class="info-value transaction-id">{{ $money->userid }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">YuanBao:</span>
                    <span class="info-value coins-text">{{ number_format($money->YuanBao) }} YB</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Money (Zen):</span>
                    <span class="info-value amount-text">{{ number_format($money->Money) }} Zen</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày tạo:</span>
                    <span class="info-value">{{ $money->CreateTime ? date('d/m/Y H:i:s', strtotime($money->CreateTime)) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cập nhật cuối:</span>
                    <span class="info-value">{{ $money->UpdateTime ? date('d/m/Y H:i:s', strtotime($money->UpdateTime)) : 'N/A' }}</span>
                </div>
            </div>

            <!-- Account Information -->
            <div class="info-card">
                <h3 class="card-title">👤 Thông tin tài khoản</h3>
                <div class="info-row">
                    <span class="info-label">ID:</span>
                    <span class="info-value">{{ $account->ID }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên tài khoản:</span>
                    <span class="info-value">{{ $account->UserName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $account->Email ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="info-value">{{ $account->Status == 1 ? 'Hoạt động' : 'Bị khóa' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày tạo:</span>
                    <span class="info-value">{{ $account->CreateTime ? date('d/m/Y H:i:s', strtotime($account->CreateTime)) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Đăng nhập cuối:</span>
                    <span class="info-value">{{ $account->LastLoginTime ? date('d/m/Y H:i:s', strtotime($account->LastLoginTime)) : 'Chưa đăng nhập' }}</span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="info-card">
                <h3 class="card-title">⚡ Thao tác nhanh</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                    <a href="{{ route('admin.game-money.edit', $account->ID) }}" class="btn btn-primary">
                        ✏️ Chỉnh sửa xu
                    </a>
                    <a href="{{ route('admin.accounts.show', $account->ID) }}" class="btn btn-info">
                        👁️ Xem tài khoản
                    </a>
                    <a href="{{ route('admin.accounts.edit', $account->ID) }}" class="btn btn-warning">
                        🔧 Sửa tài khoản
                    </a>
                </div>
                <div class="info-row">
                    <span class="info-label">Tổng tài sản:</span>
                    <span class="info-value coins-text">{{ number_format($money->YuanBao + $money->Money) }}</span>
                </div>
            </div>
        </div>

        <!-- Account Timeline -->
        <div class="timeline">
            <h3 class="timeline-title">⏰ Thông tin tài khoản</h3>

            <div class="timeline-item">
                <div class="timeline-icon">👤</div>
                <div class="timeline-content">
                    <div class="timeline-action">Tài khoản được tạo</div>
                    <div class="timeline-time">{{ $account->CreateTime ? date('d/m/Y H:i:s', strtotime($account->CreateTime)) : 'N/A' }}</div>
                </div>
            </div>

            @if($account->LastLoginTime)
                <div class="timeline-item">
                    <div class="timeline-icon">🔑</div>
                    <div class="timeline-content">
                        <div class="timeline-action">Đăng nhập lần cuối</div>
                        <div class="timeline-time">{{ date('d/m/Y H:i:s', strtotime($account->LastLoginTime)) }}</div>
                    </div>
                </div>
            @endif

            @if($money->UpdateTime)
                <div class="timeline-item">
                    <div class="timeline-icon">💰</div>
                    <div class="timeline-content">
                        <div class="timeline-action">Cập nhật xu game lần cuối</div>
                        <div class="timeline-time">{{ date('d/m/Y H:i:s', strtotime($money->UpdateTime)) }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
