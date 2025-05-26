@extends('layouts.admin')

@section('title', 'Chi tiết IP {{ $ipDetails->ip_address }} - MU Admin Panel')

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
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            color: #3b82f6;
            font-size: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
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
        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
            text-align: center;
        }
        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            opacity: 0.8;
            font-size: 14px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .content-card {
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
        .user-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .user-info {
            flex: 1;
        }
        .user-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .user-email {
            font-size: 12px;
            opacity: 0.7;
        }
        .user-stats {
            text-align: right;
            font-size: 14px;
        }
        .login-count {
            font-weight: 600;
            color: #3b82f6;
        }
        .last-login {
            font-size: 12px;
            opacity: 0.7;
        }
        .activity-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .activity-user {
            font-weight: 600;
        }
        .activity-time {
            font-size: 12px;
            opacity: 0.7;
        }
        .activity-action {
            font-size: 14px;
            opacity: 0.8;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-success {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status-blocked {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .ban-form {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-top: 30px;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
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
        .alert alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .banned-notice {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .banned-notice h3 {
            margin-bottom: 10px;
            color: #ef4444;
        }
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: white;
            opacity: 0.7;
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
                <h1 class="page-title">🌐 Chi tiết IP</h1>
                <div class="ip-address">{{ $ipDetails->ip_address }}</div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.ip-management.index') }}" class="btn btn-primary">← Quay lại</a>
                @if($isBanned)
                    <form action="{{ route('admin.ip-management.unban', $ipDetails->ip_address) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn bỏ cấm IP này?')">✅ Bỏ cấm IP</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        <!-- Banned Notice -->
        @if($isBanned)
            <div class="banned-notice">
                <h3>🚫 IP này đã bị cấm</h3>
                <p>IP address này hiện đang bị cấm truy cập hệ thống</p>
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🔢</div>
                <div class="stat-value">{{ number_format($ipDetails->total_logins) }}</div>
                <div class="stat-label">Tổng lượt truy cập</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ number_format($ipDetails->unique_users) }}</div>
                <div class="stat-label">Người dùng unique</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">{{ date('d/m/Y', strtotime($ipDetails->first_seen)) }}</div>
                <div class="stat-label">Lần đầu thấy</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏰</div>
                <div class="stat-value">{{ date('d/m/Y H:i', strtotime($ipDetails->last_seen)) }}</div>
                <div class="stat-label">Lần cuối thấy</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Users from this IP -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">👥 Người dùng từ IP này ({{ count($users) }})</h3>
                </div>
                <div class="card-body">
                    @if(count($users) > 0)
                        <div class="user-list">
                            @foreach($users as $user)
                                <div class="user-item">
                                    <div class="user-info">
                                        <div class="user-name">{{ $user->username }}</div>
                                        @if($user->email)
                                            <div class="user-email">{{ $user->email }}</div>
                                        @endif
                                    </div>
                                    <div class="user-stats">
                                        <div class="login-count">{{ number_format($user->login_count) }} lượt</div>
                                        <div class="last-login">{{ date('d/m/Y H:i', strtotime($user->last_login)) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">Chưa có người dùng nào</div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">📋 Hoạt động gần đây ({{ count($activities) }})</h3>
                </div>
                <div class="card-body">
                    @if(count($activities) > 0)
                        <div class="activity-list">
                            @foreach($activities as $activity)
                                <div class="activity-item">
                                    <div class="activity-header">
                                        <span class="activity-user">{{ $activity->username }}</span>
                                        <span class="activity-time">{{ date('d/m/Y H:i:s', strtotime($activity->created_at)) }}</span>
                                    </div>
                                    <div class="activity-action">
                                        {{ ucfirst($activity->action) }}
                                        @if($activity->character_name)
                                            - {{ $activity->character_name }}
                                        @endif
                                        <span class="status-badge status-{{ $activity->status }}">
                                            @switch($activity->status)
                                                @case('success')
                                                    Thành công
                                                    @break
                                                @case('failed')
                                                    Thất bại
                                                    @break
                                                @case('blocked')
                                                    Bị chặn
                                                    @break
                                                @default
                                                    {{ $activity->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">Chưa có hoạt động nào</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ban IP Form -->
        @if(!$isBanned)
            <div class="ban-form">
                <h3 style="margin-bottom: 20px; color: #ef4444;">🚫 Cấm IP Address</h3>
                <form action="{{ route('admin.ip-management.ban', $ipDetails->ip_address) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Lý do cấm *</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Nhập lý do cấm IP này..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="expires_at">Thời hạn cấm (tùy chọn)</label>
                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                        <small style="opacity: 0.7; font-size: 12px;">Để trống nếu muốn cấm vĩnh viễn</small>
                    </div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn cấm IP này?')">
                        🚫 Cấm IP
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
