@extends('layouts.admin')

@section('title', 'Admin Logs - MU Admin Panel')

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
        .export-buttons {
            display: flex;
            gap: 10px;
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
        .stat-detail {
            font-size: 12px;
            opacity: 0.6;
            margin-top: 10px;
        }
        .filters-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
        }
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
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
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .logs-table {
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
        }
        .table-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
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
            gap: 8px;
        }
        .admin-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        .admin-details {
            flex: 1;
        }
        .admin-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .admin-ip {
            font-size: 12px;
            opacity: 0.7;
            font-family: 'Courier New', monospace;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
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
        .reason-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">📝 Admin Logs</h1>
                <p class="page-subtitle">Theo dõi và kiểm tra lịch sử hành động của admin</p>
            </div>
            <div class="export-buttons">
                <a href="{{ route('admin.logs.export', request()->query()) }}" class="btn btn-success">📤 Xuất CSV</a>
                <a href="{{ route('admin.logs.login-logs') }}" class="btn btn-primary">🔐 Login Logs</a>
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
                <div class="stat-icon">📊</div>
                <div class="stat-value">{{ number_format($stats['total_actions']) }}</div>
                <div class="stat-label">Tổng hành động</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">{{ number_format($stats['today_actions']) }}</div>
                <div class="stat-label">Hành động hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value">{{ number_format($stats['unique_admins']) }}</div>
                <div class="stat-label">Admin hoạt động</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-value">{{ $stats['most_active_admin']->username ?? 'N/A' }}</div>
                <div class="stat-label">Admin tích cực nhất</div>
                @if($stats['most_active_admin'])
                    <div class="stat-detail">{{ number_format($stats['most_active_admin']->action_count) }} hành động</div>
                @endif
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="filters-form">
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="Target name, reason, admin..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label>Admin</label>
                    <select name="admin_filter" class="form-control">
                        <option value="all" {{ $adminFilter == 'all' ? 'selected' : '' }}>Tất cả admin</option>
                        @foreach($admins as $adminOption)
                            <option value="{{ $adminOption->id }}" {{ $adminFilter == $adminOption->id ? 'selected' : '' }}>
                                {{ $adminOption->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Hành động</label>
                    <select name="action_filter" class="form-control">
                        <option value="all" {{ $actionFilter == 'all' ? 'selected' : '' }}>Tất cả hành động</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ $actionFilter == $action ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Loại target</label>
                    <select name="target_filter" class="form-control">
                        <option value="all" {{ $targetFilter == 'all' ? 'selected' : '' }}>Tất cả loại</option>
                        @foreach($targetTypes as $type)
                            <option value="{{ $type }}" {{ $targetFilter == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Từ ngày</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="form-group">
                    <label>Đến ngày</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">🔍 Lọc</button>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="logs-table">
            <div class="table-header">
                <h3 class="table-title">📋 Lịch sử hành động admin ({{ $logs->total() }} records)</h3>
            </div>

            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Hành động</th>
                                <th>Target</th>
                                <th>Lý do</th>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <div class="admin-info">
                                            <div class="admin-avatar">
                                                {{ strtoupper(substr($log->admin_username, 0, 2)) }}
                                            </div>
                                            <div class="admin-details">
                                                <div class="admin-name">{{ $log->admin_username }}</div>
                                                <div class="admin-ip">{{ $log->ip_address }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <div>
                                            <span class="target-badge">{{ ucfirst($log->target_type) }}</span>
                                            <br>
                                            <strong>{{ $log->target_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="reason-text" title="{{ $log->reason }}">
                                            {{ $log->reason }}
                                        </div>
                                    </td>
                                    <td>{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.logs.show', $log->id) }}" class="btn btn-info btn-sm">👁️ Chi tiết</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $logs->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>📝 Chưa có log nào</h3>
                    <p>Logs sẽ hiển thị khi admin thực hiện các hành động</p>
                </div>
            @endif
        </div>
@endsection
