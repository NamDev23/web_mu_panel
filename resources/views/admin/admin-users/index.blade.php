@extends('layouts.admin')

@section('title', 'Quản lý Admin Users - MU Admin Panel')

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
        display: flex;
        gap: 15px;
        align-items: end;
    }
    .form-group {
        flex: 1;
        min-width: 0;
    }
    .form-group.search-type {
        flex: 0 0 200px;
    }
    .form-group.search-input {
        flex: 2;
    }
    .form-group.search-button {
        flex: 0 0 auto;
    }
    .form-group label {
        display: block;
        color: white;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .form-control {
        width: 100%;
        padding: 12px;
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
    .btn-primary {
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        color: white;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
    }
    .btn-success {
        background: linear-gradient(45deg, #10b981, #059669);
        color: white;
    }
    .btn-success:hover {
        transform: translateY(-2px);
    }
    .admin-users-table {
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
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
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
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
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
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
    .btn-info {
        background: linear-gradient(45deg, #06b6d4, #0891b2);
        color: white;
    }
    .no-results {
        text-align: center;
        padding: 40px;
        color: white;
        opacity: 0.8;
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
    .create-button {
        margin-bottom: 20px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: stretch;
        }
        .form-group.search-type,
        .form-group.search-input,
        .form-group.search-button {
            flex: none;
            width: 100%;
        }
        .form-group.search-button {
            margin-top: 10px;
        }
        .admin-users-table {
            overflow-x: auto;
        }
        .table {
            min-width: 800px;
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
        <h1 class="page-title">👥 Quản lý Admin Users</h1>
        <p class="page-desc">Tìm kiếm, xem thông tin và quản lý tài khoản admin</p>
    </div>

    <!-- Create Button -->
    @if($admin['role'] === 'super_admin')
        <div class="create-button">
            <a href="{{ route('admin.admin-users.create') }}" class="btn btn-success">
                ➕ Tạo Admin User mới
            </a>
        </div>
    @endif

    <!-- Search Section -->
    <div class="search-section">
        <form class="search-form" method="GET">
            <div class="form-group search-type">
                <label>Lọc theo role</label>
                <select name="role" class="form-control">
                    <option value="">Tất cả</option>
                    <option value="super_admin" {{ $role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="moderator" {{ $role == 'moderator' ? 'selected' : '' }}>Moderator</option>
                </select>
            </div>
            <div class="form-group search-input">
                <label>Từ khóa tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên đăng nhập, email, họ tên..." value="{{ $search }}">
            </div>
            <div class="form-group search-button">
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </div>
        </form>
    </div>

    <!-- Admin Users Table -->
    <div class="admin-users-table">
        @if(count($adminUsers) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Họ và tên</th>
                        <th>Role</th>
                        <th>Trạng thái</th>
                        <th>Đăng nhập cuối</th>
                        <th>Tạo lúc</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adminUsers as $adminUser)
                        <tr>
                            <td>{{ $adminUser->id }}</td>
                            <td><strong>{{ $adminUser->username }}</strong></td>
                            <td>{{ $adminUser->email }}</td>
                            <td>{{ $adminUser->full_name }}</td>
                            <td>
                                <span class="role-badge role-{{ str_replace('_', '-', $adminUser->role) }}">
                                    {{ $adminUser->getRoleText() }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $adminUser->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $adminUser->getStatusText() }}
                                </span>
                            </td>
                            <td>{{ $adminUser->last_login_at ? $adminUser->last_login_at->format('d/m/Y H:i') : 'Chưa đăng nhập' }}</td>
                            <td>{{ $adminUser->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.admin-users.show', $adminUser->id) }}" class="btn btn-info btn-sm">👁️ Xem</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $adminUsers->appends(request()->query())->links('pagination.custom') }}
        @else
            <div class="no-results">
                <h3>🔍 Không tìm thấy admin user nào</h3>
                <p>Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
            </div>
        @endif
    </div>
@endsection
