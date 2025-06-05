@extends('layouts.admin')

@section('title', 'Chi tiết xu game - Admin Panel')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .page-desc {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }
    .info-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .info-item {
        background: rgba(255, 255, 255, 0.05);
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .info-label {
        font-size: 0.9rem;
        color: #94a3b8;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }
    .money-value {
        color: #fbbf24;
    }
    .realmoney-value {
        color: #3b82f6;
    }
    .total-value {
        color: #10b981;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .status-banned {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">💰 Chi tiết xu game</h1>
            <p class="page-desc">Thông tin chi tiết xu game của tài khoản {{ $account->UserName }}</p>
        </div>
    </div>

    <!-- Account Info -->
    <div class="info-card">
        <h3 style="margin-bottom: 1.5rem; color: #1e293b;">👤 Thông tin tài khoản</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">ID Tài khoản</div>
                <div class="info-value">{{ $account->ID }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tên tài khoản</div>
                <div class="info-value">{{ $account->UserName }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $account->Email ?: 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Trạng thái</div>
                <div class="info-value">
                    <span class="status-badge {{ $account->Status == 1 ? 'status-active' : 'status-banned' }}">
                        {{ $account->Status == 1 ? 'Hoạt động' : 'Bị khóa' }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Ngày tạo</div>
                <div class="info-value">{{ $account->CreateTime ? date('d/m/Y H:i', strtotime($account->CreateTime)) : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Game User ID</div>
                <div class="info-value">{{ 'ZT' . str_pad($account->ID, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    <!-- Money Info -->
    <div class="info-card">
        <h3 style="margin-bottom: 1.5rem; color: #1e293b;">💰 Thông tin xu game</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">RealMoney</div>
                <div class="info-value realmoney-value">{{ number_format($money->realmoney) }} RM</div>
            </div>
            <div class="info-item">
                <div class="info-label">Money (Zen)</div>
                <div class="info-value money-value">{{ number_format($money->money) }} Zen</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tổng tài sản</div>
                <div class="info-value total-value">{{ number_format($money->realmoney + $money->money) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Gift ID</div>
                <div class="info-value">{{ number_format($money->giftid) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Gift Jifen</div>
                <div class="info-value">{{ number_format($money->giftjifen) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Points</div>
                <div class="info-value">{{ number_format($money->points) }}</div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin.game-money.index') }}" class="btn btn-secondary">
                ← Quay lại danh sách
            </a>
            <a href="{{ route('admin.game-money.edit', $account->ID) }}" class="btn btn-primary">
                ✏️ Chỉnh sửa xu
            </a>
        </div>
    </div>
@endsection
