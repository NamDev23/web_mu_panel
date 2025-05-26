@extends('layouts.admin')

@section('title', 'Quản lý IP - MU Admin Panel')

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
        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .nav-tab {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-tab.active, .nav-tab:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
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
        .search-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group label {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        .form-control {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            min-width: 200px;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
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
        .ip-table {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        .table-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .btn-warning {
            background: linear-gradient(45deg, #f59e0b, #d97706);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            color: white;
            font-size: 14px;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #3b82f6;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-normal {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-suspicious {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-info {
            background: linear-gradient(45deg, #06b6d4, #0891b2);
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: white;
            opacity: 0.7;
        }
        .no-data h3 {
            font-size: 18px;
            margin-bottom: 10px;
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .top-ips {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
            color: white;
        }
        .top-ips-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .top-ip-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .top-ip-item:last-child {
            border-bottom: none;
        }
        .top-ip-info {
            flex: 1;
        }
        .top-ip-address {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .top-ip-detail {
            font-size: 12px;
            opacity: 0.7;
        }
        .top-ip-stats {
            text-align: right;
            font-weight: 600;
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">🌐 Quản lý IP</h1>
            <p class="page-subtitle">Theo dõi và quản lý địa chỉ IP truy cập hệ thống</p>

            <div class="nav-tabs">
                <a href="{{ route('admin.ip-management.index') }}" class="nav-tab active">📊 Tổng quan</a>
                <a href="{{ route('admin.ip-management.banned') }}" class="nav-tab">🚫 IP bị cấm</a>
                <a href="{{ route('admin.ip-management.suspicious') }}" class="nav-tab">⚠️ IP đáng nghi</a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🌐</div>
                <div class="stat-value">{{ number_format($stats['total_ips']) }}</div>
                <div class="stat-label">Tổng IP unique</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">{{ number_format($stats['unique_today']) }}</div>
                <div class="stat-label">IP unique hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🚫</div>
                <div class="stat-value">{{ number_format($stats['banned_ips']) }}</div>
                <div class="stat-label">IP bị cấm</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-value">{{ number_format($stats['suspicious_ips']) }}</div>
                <div class="stat-label">IP đáng nghi</div>
            </div>
        </div>

        <!-- Top IPs -->
        <div class="top-ips">
            <h3 class="top-ips-title">🔥 Top IP hoạt động nhiều nhất</h3>
            @if(count($topIps) > 0)
                @foreach($topIps as $ip)
                    <div class="top-ip-item">
                        <div class="top-ip-info">
                            <div class="top-ip-address">{{ $ip->ip_address }}</div>
                            <div class="top-ip-detail">{{ $ip->user_count }} người dùng</div>
                        </div>
                        <div class="top-ip-stats">{{ number_format($ip->login_count) }} lượt đăng nhập</div>
                    </div>
                @endforeach
            @else
                <div class="no-data">Chưa có dữ liệu</div>
            @endif
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('admin.ip-management.index') }}" class="search-form">
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="IP, username, character..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="success" {{ $statusFilter == 'success' ? 'selected' : '' }}>Thành công</option>
                        <option value="failed" {{ $statusFilter == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        <option value="blocked" {{ $statusFilter == 'blocked' ? 'selected' : '' }}>Bị chặn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Hành động</label>
                    <select name="action" class="form-control">
                        <option value="all" {{ $actionFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="login" {{ $actionFilter == 'login' ? 'selected' : '' }}>Đăng nhập</option>
                        <option value="logout" {{ $actionFilter == 'logout' ? 'selected' : '' }}>Đăng xuất</option>
                        <option value="register" {{ $actionFilter == 'register' ? 'selected' : '' }}>Đăng ký</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- IP Logs Table -->
        <div class="ip-table">
            <div class="table-header">
                <h3 class="table-title">📋 Lịch sử truy cập IP ({{ $ipLogs->total() }} records)</h3>
                <div class="export-buttons">
                    <a href="{{ route('admin.ip-management.export', ['type' => 'all']) }}" class="btn btn-success btn-sm">📤 Xuất tất cả</a>
                    <a href="{{ route('admin.ip-management.export', ['type' => 'suspicious']) }}" class="btn btn-warning btn-sm">⚠️ Xuất đáng nghi</a>
                    <a href="{{ route('admin.ip-management.export', ['type' => 'banned']) }}" class="btn btn-danger btn-sm">🚫 Xuất bị cấm</a>
                </div>
            </div>

            @if($ipLogs->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Tài khoản</th>
                                <th>Nhân vật</th>
                                <th>Hành động</th>
                                <th>Trạng thái</th>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ipLogs as $log)
                                <tr>
                                    <td>
                                        <span class="ip-address">{{ $log->ip_address }}</span>
                                    </td>
                                    <td>
                                        {{ $log->username }}
                                        @if($log->email)
                                            <br><small style="opacity: 0.7;">{{ $log->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $log->character_name ?: 'N/A' }}</td>
                                    <td>{{ ucfirst($log->action) }}</td>
                                    <td>
                                        @switch($log->status)
                                            @case('success')
                                                <span class="status-badge status-normal">Thành công</span>
                                                @break
                                            @case('failed')
                                                <span class="status-badge status-suspicious">Thất bại</span>
                                                @break
                                            @case('blocked')
                                                <span class="status-badge status-banned">Bị chặn</span>
                                                @break
                                            @default
                                                <span class="status-badge status-normal">{{ $log->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.ip-management.show', $log->ip_address) }}" class="btn btn-info btn-sm">👁️ Chi tiết</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $ipLogs->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>🌐 Chưa có log IP nào</h3>
                    <p>Logs sẽ hiển thị khi có người dùng truy cập hệ thống</p>
                </div>
            @endif
        </div>
@endsection
