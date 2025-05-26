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
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/admin/dashboard">Dashboard</a> / 
            <a href="/admin/accounts">Quản lý tài khoản</a> / 
            <a href="/admin/accounts/{{ $account->id }}">{{ $account->username }}</a> / 
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
                <p>Cập nhật thông tin tài khoản: <strong>{{ $account->username }}</strong></p>
            </div>

            <!-- Account Status Info -->
            <div class="status-info">
                <span>Trạng thái hiện tại:</span>
                <span class="status-badge {{ $account->status == 'active' ? 'status-active' : 'status-banned' }}">
                    {{ $account->status == 'active' ? 'Hoạt động' : 'Bị khóa' }}
                </span>
                <span class="vip-badge">VIP {{ $account->vip_level }}</span>
            </div>

            <div class="info-note">
                ℹ️ <strong>Lưu ý:</strong> Việc thay đổi thông tin tài khoản sẽ được ghi lại trong log hệ thống. 
                Chỉ thay đổi những thông tin cần thiết và đảm bảo tính chính xác.
            </div>

            <form action="{{ route('admin.accounts.update', $account->id) }}" method="POST">
                @csrf
                
                <div class="form-grid">
                    <!-- Username (readonly) -->
                    <div class="form-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" class="form-control" value="{{ $account->username }}" readonly>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $account->email) }}" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $account->phone) }}" placeholder="Nhập số điện thoại">
                    </div>

                    <!-- Full Name -->
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $account->full_name) }}" placeholder="Nhập họ và tên">
                    </div>

                    <!-- VIP Level -->
                    <div class="form-group">
                        <label>Cấp VIP *</label>
                        <select name="vip_level" class="form-control" required>
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('vip_level', $account->vip_level) == $i ? 'selected' : '' }}>
                                    VIP {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Current Balance -->
                    <div class="form-group">
                        <label>Số dư hiện tại *</label>
                        <input type="number" name="current_balance" class="form-control" value="{{ old('current_balance', $account->current_balance) }}" min="0" step="0.01" required>
                    </div>

                    <!-- Total Recharge (readonly) -->
                    <div class="form-group">
                        <label>Tổng nạp (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ number_format($account->total_recharge) }}đ" readonly>
                    </div>

                    <!-- Characters Count (readonly) -->
                    <div class="form-group">
                        <label>Số nhân vật (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ $account->characters_count }} nhân vật" readonly>
                    </div>

                    <!-- Registration Date (readonly) -->
                    <div class="form-group">
                        <label>Ngày đăng ký (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ date('d/m/Y H:i', strtotime($account->created_at)) }}" readonly>
                    </div>

                    <!-- Last Login (readonly) -->
                    <div class="form-group">
                        <label>Đăng nhập cuối (chỉ đọc)</label>
                        <input type="text" class="form-control" value="{{ $account->last_login_at ? date('d/m/Y H:i', strtotime($account->last_login_at)) : 'Chưa đăng nhập' }}" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.accounts.show', $account->id) }}" class="btn btn-secondary">❌ Hủy</a>
                    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
