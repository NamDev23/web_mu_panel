@extends('layouts.admin')

@section('title', 'Chi tiết giao dịch #{{ $recharge->id }} - MU Admin Panel')

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
            <a href="/admin/coin-recharge">Quản lý nạp coin</a> /
            Giao dịch #{{ $recharge->id }}
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Transaction Header -->
        <div class="transaction-header">
            <div class="transaction-info">
                <h1>💰 Giao dịch #{{ $recharge->id }}</h1>
                <div class="transaction-meta">
                    <span class="status-badge status-{{ $recharge->status }}">
                        @switch($recharge->status)
                            @case('completed')
                                ✅ Hoàn thành
                                @break
                            @case('pending')
                                ⏳ Chờ xử lý
                                @break
                            @case('failed')
                                ❌ Thất bại
                                @break
                            @default
                                {{ $recharge->status }}
                        @endswitch
                    </span>
                    <span class="type-badge">{{ ucfirst($recharge->type) }}</span>
                    <span class="amount-badge">{{ number_format($recharge->coins_added) }} coin</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.coin-recharge.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Transaction Details -->
            <div class="info-card">
                <h3 class="card-title">📋 Chi tiết giao dịch</h3>
                <div class="info-row">
                    <span class="info-label">Mã giao dịch:</span>
                    <span class="info-value transaction-id">{{ $recharge->transaction_id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số tiền nạp:</span>
                    <span class="info-value amount-text">{{ number_format($recharge->amount) }}đ</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Coin nhận được:</span>
                    <span class="info-value coins-text">{{ number_format($recharge->coins_added) }} coin</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Loại giao dịch:</span>
                    <span class="info-value">{{ ucfirst($recharge->type) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="info-value">
                        @switch($recharge->status)
                            @case('completed')
                                ✅ Hoàn thành
                                @break
                            @case('pending')
                                ⏳ Chờ xử lý
                                @break
                            @case('failed')
                                ❌ Thất bại
                                @break
                            @default
                                {{ $recharge->status }}
                        @endswitch
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Thời gian tạo:</span>
                    <span class="info-value">{{ date('d/m/Y H:i:s', strtotime($recharge->created_at)) }}</span>
                </div>
            </div>

            <!-- Account Information -->
            <div class="info-card">
                <h3 class="card-title">👤 Thông tin tài khoản</h3>
                <div class="info-row">
                    <span class="info-label">Tên tài khoản:</span>
                    <span class="info-value">{{ $recharge->username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $recharge->email ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên nhân vật:</span>
                    <span class="info-value">{{ $recharge->character_name ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số dư hiện tại:</span>
                    <span class="info-value">{{ number_format($recharge->current_balance ?? 0) }} coin</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tổng đã nạp:</span>
                    <span class="info-value">{{ number_format($recharge->total_recharge ?? 0) }}đ</span>
                </div>
                <div class="info-row">
                    <span class="info-label">VIP Level:</span>
                    <span class="info-value">VIP {{ $recharge->vip_level ?? 0 }}</span>
                </div>
            </div>

            <!-- Admin Information -->
            <div class="info-card">
                <h3 class="card-title">🛡️ Thông tin admin</h3>
                <div class="info-row">
                    <span class="info-label">Admin thực hiện:</span>
                    <span class="info-value">{{ $recharge->admin_username }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IP admin:</span>
                    <span class="info-value">{{ $recharge->admin_ip }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Thời gian thực hiện:</span>
                    <span class="info-value">{{ date('d/m/Y H:i:s', strtotime($recharge->created_at)) }}</span>
                </div>
                
                @if($recharge->note)
                    <div class="note-section">
                        <div class="note-title">📝 Ghi chú</div>
                        <div class="note-content">{{ $recharge->note }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transaction Timeline -->
        <div class="timeline">
            <h3 class="timeline-title">⏰ Lịch sử giao dịch</h3>
            
            <div class="timeline-item">
                <div class="timeline-icon">📝</div>
                <div class="timeline-content">
                    <div class="timeline-action">Giao dịch được tạo</div>
                    <div class="timeline-time">{{ date('d/m/Y H:i:s', strtotime($recharge->created_at)) }}</div>
                </div>
            </div>

            @if($recharge->status == 'completed')
                <div class="timeline-item">
                    <div class="timeline-icon">✅</div>
                    <div class="timeline-content">
                        <div class="timeline-action">Giao dịch hoàn thành - Coin đã được cộng vào tài khoản</div>
                        <div class="timeline-time">{{ date('d/m/Y H:i:s', strtotime($recharge->updated_at)) }}</div>
                    </div>
                </div>
            @elseif($recharge->status == 'failed')
                <div class="timeline-item">
                    <div class="timeline-icon">❌</div>
                    <div class="timeline-content">
                        <div class="timeline-action">Giao dịch thất bại</div>
                        <div class="timeline-time">{{ date('d/m/Y H:i:s', strtotime($recharge->updated_at)) }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
