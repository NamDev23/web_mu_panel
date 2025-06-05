@extends('layouts.admin')

@section('title', 'Admin Logs - MU Admin Panel')

@section('styles')
<style>
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
    .page-desc {
        opacity: 0.9;
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
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        color: white;
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 14px;
    }
    .form-control {
        padding: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .btn-primary {
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .logs-table {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 14px;
    }
    .table th {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-weight: 600;
    }
    .table td {
        color: white;
    }
    .table tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .action-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .action-login {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .action-update {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .action-delete {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .action-create {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .action-ban {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .action-unban {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .action-clear {
        background: rgba(251, 191, 36, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }
    .no-results {
        text-align: center;
        padding: 40px;
        color: white;
        opacity: 0.8;
    }
    .data-preview {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        opacity: 0.7;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .search-form {
            grid-template-columns: 1fr;
        }
        .logs-table {
            overflow-x: auto;
        }
        .table {
            min-width: 800px;
        }
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">📋 Admin Action Logs</h1>
        <p class="page-desc">Theo dõi tất cả hành động của admin trong hệ thống</p>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <form class="search-form" method="GET">
            <div class="form-group">
                <label>Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Target name, reason..." value="{{ $search }}">
            </div>
            <div class="form-group">
                <label>Action</label>
                <select name="action" class="form-control">
                    <option value="">Tất cả actions</option>
                    @foreach($actions as $actionOption)
                        <option value="{{ $actionOption }}" {{ $action == $actionOption ? 'selected' : '' }}>
                            {{ $actionOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Admin</label>
                <select name="admin_filter" class="form-control">
                    <option value="">Tất cả admins</option>
                    @foreach($admins as $adminOption)
                        <option value="{{ $adminOption }}" {{ $adminFilter == $adminOption ? 'selected' : '' }}>
                            {{ $adminOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table">
        @if(count($logs) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Reason</th>
                        <th>IP Address</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</td>
                            <td><strong>{{ $log->admin_username }}</strong></td>
                            <td>
                                <span class="action-badge action-{{ str_replace('_', '-', explode('_', $log->action)[0]) }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $log->target_type }}</div>
                                <div style="font-size: 12px; opacity: 0.7;">{{ $log->target_name }}</div>
                            </td>
                            <td>{{ $log->reason }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>
                                @if($log->old_data && $log->old_data !== '[]')
                                    <div class="data-preview" title="{{ $log->old_data }}">
                                        Old: {{ Str::limit($log->old_data, 30) }}
                                    </div>
                                @endif
                                @if($log->new_data && $log->new_data !== '[]')
                                    <div class="data-preview" title="{{ $log->new_data }}">
                                        New: {{ Str::limit($log->new_data, 30) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $logs->appends(request()->query())->links('pagination.custom') }}
        @else
            <div class="no-results">
                <h3>📋 Không tìm thấy log nào</h3>
                <p>Thử thay đổi bộ lọc tìm kiếm</p>
            </div>
        @endif
    </div>
@endsection
