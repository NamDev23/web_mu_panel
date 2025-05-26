@extends('layouts.admin')

@section('title', 'Chi tiết Admin Log #{{ $log->id }} - MU Admin Panel')

@section('styles')
<style>
        .nav-links a:hover, .nav-links a.active {
            background: rgba(255, 255, 255, 0.1);
        }
        .page-header {
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
        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .page-subtitle {
            opacity: 0.8;
            font-size: 16px;
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
            font-size: 14px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #2563eb);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .log-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .detail-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        .card-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .card-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        .card-
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            opacity: 0.8;
            min-width: 120px;
        }
        .detail-value {
            flex: 1;
            text-align: right;
        }
        .action-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .action-create {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .action-edit {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .action-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .action-ban {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .target-badge {
            background: rgba(107, 114, 128, 0.3);
            color: #d1d5db;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        .admin-details {
            flex: 1;
        }
        .admin-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .admin-email {
            font-size: 12px;
            opacity: 0.7;
        }
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #3b82f6;
        }
        .data-comparison {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            grid-column: 1 / -1;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .old-data, .new-data {
            padding: 25px;
        }
        .old-data {
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }
        .data-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: white;
        }
        .old-data .data-title {
            color: #ef4444;
        }
        .new-data .data-title {
            color: #10b981;
        }
        .data-content {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: white;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: white;
            opacity: 0.7;
        }
        .reason-box {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            color: white;
        }
        .reason-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #3b82f6;
        }
        .reason-text {
            line-height: 1.6;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">📝 Chi tiết Admin Log</h1>
                <p class="page-subtitle">Log ID: #{{ $log->id }}</p>
            </div>
            <a href="{{ route('admin.logs.index') }}" class="btn btn-primary">← Quay lại</a>
        </div>

        <!-- Reason Box -->
        @if($log->reason)
            <div class="reason-box">
                <h3 class="reason-title">💬 Lý do thực hiện</h3>
                <div class="reason-text">{{ $log->reason }}</div>
            </div>
        @endif

        <!-- Log Details -->
        <div class="log-details">
            <!-- Basic Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3 class="card-title">ℹ️ Thông tin cơ bản</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Hành động:</span>
                        <div class="detail-value">
                            @php
                                $actionClass = 'action-create';
                                if (str_contains($log->action, 'edit') || str_contains($log->action, 'update')) {
                                    $actionClass = 'action-edit';
                                } elseif (str_contains($log->action, 'delete') || str_contains($log->action, 'destroy')) {
                                    $actionClass = 'action-delete';
                                } elseif (str_contains($log->action, 'ban') || str_contains($log->action, 'block')) {
                                    $actionClass = 'action-ban';
                                }
                            @endphp
                            <span class="action-badge {{ $actionClass }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Target:</span>
                        <div class="detail-value">
                            <span class="target-badge">{{ ucfirst($log->target_type) }}</span>
                            <br>
                            <strong>{{ $log->target_name }}</strong>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Target ID:</span>
                        <div class="detail-value">{{ $log->target_id }}</div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Thời gian:</span>
                        <div class="detail-value">{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</div>
                    </div>
                </div>
            </div>

            <!-- Admin Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3 class="card-title">👤 Thông tin Admin</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Admin:</span>
                        <div class="detail-value">
                            <div class="admin-info">
                                <div class="admin-avatar">
                                    {{ strtoupper(substr($log->admin_username, 0, 2)) }}
                                </div>
                                <div class="admin-details">
                                    <div class="admin-name">{{ $log->admin_username }}</div>
                                    @if($log->admin_email)
                                        <div class="admin-email">{{ $log->admin_email }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">IP Address:</span>
                        <div class="detail-value">
                            <span class="ip-address">{{ $log->ip_address }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">User Agent:</span>
                        <div class="detail-value" style="font-size: 12px; opacity: 0.8; word-break: break-all;">
                            {{ $log->user_agent }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Comparison -->
            @if($log->old_data || $log->new_data)
                <div class="data-comparison">
                    <div class="card-header">
                        <h3 class="card-title">🔄 So sánh dữ liệu</h3>
                    </div>
                    <div class="comparison-grid">
                        <div class="old-data">
                            <h4 class="data-title">📤 Dữ liệu cũ</h4>
                            <div class="data-content">
                                @if($log->old_data)
                                    {{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    <div class="no-data">Không có dữ liệu cũ</div>
                                @endif
                            </div>
                        </div>
                        <div class="new-data">
                            <h4 class="data-title">📥 Dữ liệu mới</h4>
                            <div class="data-content">
                                @if($log->new_data)
                                    {{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    <div class="no-data">Không có dữ liệu mới</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
