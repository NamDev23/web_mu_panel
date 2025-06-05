@extends('layouts.admin')

@section('title', 'Chi tiết Admin User - MU Admin Panel')

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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .page-desc {
        opacity: 0.9;
    }
    .info-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 30px;
        margin-bottom: 30px;
        color: white;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    .info-item {
        margin-bottom: 20px;
    }
    .info-label {
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 5px;
        font-size: 14px;
    }
    .info-value {
        font-size: 16px;
        color: white;
    }
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }
    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .role-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }
    .role-super-admin {
        background: linear-gradient(45deg, #fbbf24, #f59e0b);
        color: white;
    }
    .role-admin {
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        color: white;
    }
    .role-moderator {
        background: linear-gradient(45deg, #6b7280, #374151);
        color: white;
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
        margin-right: 10px;
    }
    .btn-primary {
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        color: white;
    }
    .btn-success {
        background: linear-gradient(45deg, #10b981, #059669);
        color: white;
    }
    .btn-danger {
        background: linear-gradient(45deg, #ef4444, #dc2626);
        color: white;
    }
    .btn-secondary {
        background: linear-gradient(45deg, #6b7280, #4b5563);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .permissions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .permission-tag {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .logs-table {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        margin-top: 30px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th,
    .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .table th {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-weight: 600;
        font-size: 14px;
    }
    .table td {
        color: white;
        font-size: 14px;
    }
    .table tr:hover {
        background: rgba(255, 255, 255, 0.05);
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
    .alert-error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: center;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        .action-buttons {
            width: 100%;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
        .logs-table {
            overflow-x: auto;
        }
        .table {
            min-width: 600px;
        }
    }
</style>
@endsection

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="success-message">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                ❌ {{ $error }}
            @endforeach
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">👤 Chi tiết Admin User</h1>
            <p class="page-desc">Thông tin chi tiết của {{ $adminUser->username }}</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">
                ← Quay lại
            </a>
            @if($admin['role'] === 'super_admin' || $admin['id'] == $adminUser->id)
                <a href="{{ route('admin.admin-users.edit', $adminUser->id) }}" class="btn btn-primary">
                    ✏️ Chỉnh sửa
                </a>
            @endif
            @if($admin['role'] === 'super_admin' && $admin['id'] != $adminUser->id)
                <form action="{{ route('admin.admin-users.toggle-status', $adminUser->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn {{ $adminUser->is_active ? 'btn-danger' : 'btn-success' }}" 
                            onclick="return confirm('Bạn có chắc chắn muốn {{ $adminUser->is_active ? 'vô hiệu hóa' : 'kích hoạt' }} admin user này?')">
                        {{ $adminUser->is_active ? '🚫 Vô hiệu hóa' : '✅ Kích hoạt' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Admin User Information -->
    <div class="info-card">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            📋 Thông tin cơ bản
        </h2>
        
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">ID</div>
                    <div class="info-value">{{ $adminUser->id }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tên đăng nhập</div>
                    <div class="info-value"><strong>{{ $adminUser->username }}</strong></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $adminUser->email }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Họ và tên</div>
                    <div class="info-value">{{ $adminUser->full_name }}</div>
                </div>
            </div>
            
            <div>
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div class="info-value">
                        <span class="role-badge role-{{ str_replace('_', '-', $adminUser->role) }}">
                            {{ $adminUser->getRoleText() }}
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <span class="status-badge {{ $adminUser->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $adminUser->getStatusText() }}
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Đăng nhập cuối</div>
                    <div class="info-value">
                        {{ $adminUser->last_login_at ? $adminUser->last_login_at->format('d/m/Y H:i:s') : 'Chưa đăng nhập' }}
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">IP đăng nhập cuối</div>
                    <div class="info-value">{{ $adminUser->last_login_ip ?: 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Quyền hạn</div>
            <div class="info-value">
                @if($adminUser->isSuperAdmin())
                    <span class="permission-tag" style="background: rgba(251, 191, 36, 0.2); color: #fbbf24; border-color: rgba(251, 191, 36, 0.3);">
                        Toàn quyền (Super Admin)
                    </span>
                @else
                    <div class="permissions-list">
                        @if($adminUser->permissions && count($adminUser->permissions) > 0)
                            @foreach($adminUser->permissions as $permission)
                                <span class="permission-tag">{{ $permission }}</span>
                            @endforeach
                        @else
                            <span style="opacity: 0.7;">Chưa có quyền nào được cấp</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <div class="info-label">Tạo lúc</div>
                        <div class="info-value">{{ $adminUser->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tạo bởi</div>
                        <div class="info-value">
                            @if($adminUser->creator)
                                {{ $adminUser->creator->username }} ({{ $adminUser->creator->full_name }})
                            @else
                                System
                            @endif
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="info-item">
                        <div class="info-label">Cập nhật lúc</div>
                        <div class="info-value">{{ $adminUser->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Cập nhật bởi</div>
                        <div class="info-value">
                            @if($adminUser->updater)
                                {{ $adminUser->updater->username }} ({{ $adminUser->updater->full_name }})
                            @else
                                System
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Login Logs -->
    @if(count($recentLogins) > 0)
        <div class="info-card">
            <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                📝 Lịch sử đăng nhập gần đây
            </h2>
            
            <div class="logs-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogins as $log)
                            <tr>
                                <td>{{ date('d/m/Y H:i:s', strtotime($log->created_at)) }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $log->user_agent }}
                                </td>
                                <td>
                                    <span class="status-badge status-active">Thành công</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
