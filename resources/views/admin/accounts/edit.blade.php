@extends('layouts.admin')

@section('title', 'Chỉnh sửa tài khoản: {{ $account->username }} - MU Admin Panel')

@section('styles')
<style>
        .breadcrumb {
            color: white;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        .breadcrumb a {
            color: white;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            color: white;
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .form-header p {
            opacity: 0.8;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: white;
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
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .form-control[readonly] {
            background: rgba(255, 255, 255, 0.05);
            opacity: 0.7;
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
        .btn-secondary {
            background: rgba(107, 114, 128, 0.8);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .info-note {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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
        .status-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
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
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-actions {
                flex-direction: column;
            }
        }
</style>
@endsection

@section('content')
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/admin/dashboard">Dashboard</a> /
            <a href="/admin/accounts">Quản lý tài khoản</a> /
            <a href="/admin/accounts/{{ $account->ID }}">{{ $account->UserName }}</a> /
            Chỉnh sửa
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    ❌ {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <!-- Edit Form -->
        <div class="content-card">
            <div class="form-header">
                <h1>✏️ Chỉnh sửa tài khoản</h1>
                <p>Cập nhật thông tin tài khoản: <strong>{{ $account->UserName }}</strong></p>
            </div>

            <!-- Account Status Info -->
            <div class="status-info">
                <span>Trạng thái hiện tại:</span>
                <span class="status-badge {{ $account->Status == 1 ? 'status-active' : 'status-banned' }}">
                    {{ $account->Status == 1 ? 'Hoạt động' : 'Bị khóa' }}
                </span>
                <span class="vip-badge">VIP 0</span>
            </div>

            <div class="info-note">
                ℹ️ <strong>Lưu ý:</strong> Việc thay đổi thông tin tài khoản sẽ được ghi lại trong log hệ thống.
                Chỉ thay đổi những thông tin cần thiết và đảm bảo tính chính xác.
            </div>

            <form action="{{ route('admin.accounts.update', $account->ID) }}" method="POST">
                @csrf

                <div class="form-grid">
                    <!-- Username (readonly) -->
                    <div class="form-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" class="form-control" value="{{ $account->UserName }}" readonly>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $account->Email) }}">
                        <small style="color: rgba(255, 255, 255, 0.7); font-size: 12px;">Có thể chỉnh sửa email tài khoản</small>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label>Trạng thái tài khoản *</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ old('status', $account->Status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('status', $account->Status) == 0 ? 'selected' : '' }}>Bị khóa</option>
                        </select>
                    </div>

                    <!-- Password Reset -->
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn thay đổi">
                        <small style="color: rgba(255, 255, 255, 0.7); font-size: 12px;">Tối thiểu 6 ký tự nếu muốn đổi mật khẩu</small>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
                    </div>

                    <!-- Game User ID (readonly) -->
                    <div class="form-group">
                        <label>Game User ID (chỉ đọc)</label>
                        <input type="text" class="form-control" value="ZT{{ str_pad($account->ID, 4, '0', STR_PAD_LEFT) }}" readonly>
                    </div>

                    <!-- Total Money (readonly) -->
                    <div class="form-group">
                        <label>Tổng xu game (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ number_format($account->total_money ?? 0) }} YB" readonly>
                    </div>

                    <!-- Characters Count (readonly) -->
                    <div class="form-group">
                        <label>Số nhân vật (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ $account->characters_count ?? 0 }} nhân vật" readonly>
                    </div>

                    <!-- Registration Date (readonly) -->
                    <div class="form-group">
                        <label>Ngày đăng ký (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ $account->CreateTime ? date('d/m/Y H:i', strtotime($account->CreateTime)) : 'N/A' }}" readonly>
                    </div>

                    <!-- Last Login (readonly) -->
                    <div class="form-group">
                        <label>Đăng nhập cuối (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ $account->LastLoginTime ? date('d/m/Y H:i', strtotime($account->LastLoginTime)) : 'Chưa đăng nhập' }}" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.accounts.show', $account->ID) }}" class="btn btn-secondary">❌ Hủy</a>
                    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
