@extends('layouts.admin')

@section('title', 'Quản lý tài khoản - MU Admin Panel')

@section('styles')
<style>
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.2);
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
        .accounts-table {
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
        .status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .vip-badge {
            background: linear-gradient(45deg, #fbbf24, #f59e0b);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
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
        .alert alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
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
            .accounts-table {
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
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    ❌ {{ $error }}
                @endforeach
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">👤 Quản lý tài khoản</h1>
            <p class="page-desc">Tìm kiếm, xem thông tin và quản lý tài khoản người chơi</p>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form class="search-form" method="GET">
                <div class="form-group search-type">
                    <label>Loại tìm kiếm</label>
                    <select name="search_type" class="form-control">
                        <option value="username" {{ $searchType == 'username' ? 'selected' : '' }}>Tên đăng nhập</option>
                        <option value="email" {{ $searchType == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="phone" {{ $searchType == 'phone' ? 'selected' : '' }}>Số điện thoại</option>
                        <option value="full_name" {{ $searchType == 'full_name' ? 'selected' : '' }}>Họ và tên</option>
                    </select>
                </div>
                <div class="form-group search-input">
                    <label>Từ khóa tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa..." value="{{ $search }}">
                </div>
                <div class="form-group search-button">
                    <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
                </div>
            </form>
        </div>

        <!-- Accounts Table -->
        <div class="accounts-table">
            @if(count($accounts) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>VIP</th>
                            <th>Trạng thái</th>
                            <th>Tổng nạp</th>
                            <th>Nhân vật</th>
                            <th>Đăng ký</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>{{ $account->id }}</td>
                                <td><strong>{{ $account->username }}</strong></td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $account->phone ?: 'N/A' }}</td>
                                <td><span class="vip-badge">VIP {{ $account->vip_level }}</span></td>
                                <td>
                                    <span class="status-badge {{ $account->status == 'active' ? 'status-active' : 'status-banned' }}">
                                        {{ $account->status == 'active' ? 'Hoạt động' : 'Bị khóa' }}
                                    </span>
                                </td>
                                <td>{{ number_format($account->total_recharge) }}đ</td>
                                <td>{{ $account->characters_count }} nhân vật</td>
                                <td>{{ date('d/m/Y', strtotime($account->created_at)) }}</td>
                                <td>
                                    <a href="/admin/accounts/{{ $account->id }}" class="btn btn-info btn-sm">👁️ Xem</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                {{ $accounts->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-results">
                    <h3>🔍 Không tìm thấy tài khoản nào</h3>
                    <p>Thử thay đổi từ khóa tìm kiếm hoặc loại tìm kiếm</p>
                </div>
            @endif
        </div>
@endsection
