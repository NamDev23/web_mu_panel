@extends('layouts.admin')

@section('title', 'IP bị cấm - MU Admin Panel')

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
        .banned-table {
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
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #ef4444;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }
        .reason-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .expires-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .expires-permanent {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .expires-temporary {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
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
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">🚫 IP bị cấm</h1>
            <p class="page-subtitle">Danh sách các IP address đã bị cấm truy cập</p>

            <div class="nav-tabs">
                <a href="{{ route('admin.ip-management.index') }}" class="nav-tab">📊 Tổng quan</a>
                <a href="{{ route('admin.ip-management.banned') }}" class="nav-tab active">🚫 IP bị cấm</a>
                <a href="{{ route('admin.ip-management.suspicious') }}" class="nav-tab">⚠️ IP đáng nghi</a>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('admin.ip-management.banned') }}" class="search-form">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="IP address hoặc lý do..." value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- Banned IPs Table -->
        <div class="banned-table">
            <div class="table-header">
                <h3 class="table-title">🚫 Danh sách IP bị cấm ({{ $bannedIps->total() }} records)</h3>
            </div>

            @if($bannedIps->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Lý do</th>
                                <th>Admin cấm</th>
                                <th>Thời gian cấm</th>
                                <th>Hết hạn</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bannedIps as $bannedIp)
                                <tr>
                                    <td>
                                        <span class="ip-address">{{ $bannedIp->ip_address }}</span>
                                    </td>
                                    <td>
                                        <div class="reason-text" title="{{ $bannedIp->reason }}">
                                            {{ $bannedIp->reason }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="admin-info">
                                            <div class="admin-avatar">
                                                {{ strtoupper(substr($bannedIp->admin_username, 0, 2)) }}
                                            </div>
                                            {{ $bannedIp->admin_username }}
                                        </div>
                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($bannedIp->banned_at)) }}</td>
                                    <td>
                                        @if($bannedIp->expires_at)
                                            <span class="expires-badge expires-temporary">
                                                {{ date('d/m/Y H:i', strtotime($bannedIp->expires_at)) }}
                                            </span>
                                        @else
                                            <span class="expires-badge expires-permanent">Vĩnh viễn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.ip-management.show', $bannedIp->ip_address) }}" class="btn btn-info btn-sm">👁️ Chi tiết</a>
                                            <form action="{{ route('admin.ip-management.unban', $bannedIp->ip_address) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc muốn bỏ cấm IP này?')">
                                                    ✅ Bỏ cấm
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $bannedIps->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>🚫 Chưa có IP nào bị cấm</h3>
                    <p>Danh sách IP bị cấm sẽ hiển thị ở đây</p>
                </div>
            @endif
        </div>
    </div>
@endsection
