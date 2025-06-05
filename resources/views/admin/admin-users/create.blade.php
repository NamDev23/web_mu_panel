@extends('layouts.admin')

@section('title', 'Tạo Admin User mới - MU Admin Panel')

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
    .form-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 30px;
        margin-bottom: 30px;
        color: white;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        color: white;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        background: rgba(255, 255, 255, 0.15);
    }
    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .form-check-input {
        width: 18px;
        height: 18px;
        accent-color: #3b82f6;
    }
    .form-check-label {
        color: white;
        font-size: 14px;
        cursor: pointer;
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
    .btn-secondary {
        background: linear-gradient(45deg, #6b7280, #4b5563);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .permissions-section {
        margin-top: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .alert-error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .required {
        color: #ef4444;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .permissions-grid {
            grid-template-columns: 1fr;
        }
        .form-actions {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert-error">
            <strong>❌ Có lỗi xảy ra:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">➕ Tạo Admin User mới</h1>
        <p class="page-desc">Tạo tài khoản admin mới cho hệ thống</p>
    </div>

    <!-- Create Form -->
    <div class="form-card">
        <form action="{{ route('admin.admin-users.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: white;">
                        📋 Thông tin cơ bản
                    </h3>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">
                            Tên đăng nhập <span class="required">*</span>
                        </label>
                        <input type="text" id="username" name="username" class="form-control" 
                               placeholder="Nhập tên đăng nhập" value="{{ old('username') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control" 
                               placeholder="Nhập địa chỉ email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name" class="form-label">
                            Họ và tên <span class="required">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" class="form-control" 
                               placeholder="Nhập họ và tên đầy đủ" value="{{ old('full_name') }}" required>
                    </div>
                </div>
                
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: white;">
                        🔐 Bảo mật
                    </h3>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">
                            Mật khẩu <span class="required">*</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            Xác nhận mật khẩu <span class="required">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                               placeholder="Nhập lại mật khẩu" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role" class="form-label">
                            Role <span class="required">*</span>
                        </label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">Chọn role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Kích hoạt tài khoản ngay
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permissions Section -->
            <div class="permissions-section">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: white;">
                    🔑 Quyền hạn
                </h3>
                <p style="opacity: 0.8; margin-bottom: 15px;">
                    Chọn các quyền hạn cho admin user này (Super Admin có toàn quyền)
                </p>
                
                <div class="permissions-grid">
                    @php
                        $permissions = [
                            'view_accounts' => 'Xem tài khoản',
                            'edit_accounts' => 'Chỉnh sửa tài khoản',
                            'ban_accounts' => 'Khóa/mở khóa tài khoản',
                            'view_characters' => 'Xem nhân vật',
                            'edit_characters' => 'Chỉnh sửa nhân vật',
                            'delete_characters' => 'Xóa nhân vật',
                            'manage_giftcodes' => 'Quản lý giftcode',
                            'manage_coins' => 'Quản lý coin',
                            'view_analytics' => 'Xem thống kê',
                            'manage_ip_bans' => 'Quản lý IP ban',
                            'view_logs' => 'Xem logs',
                        ];
                        $oldPermissions = old('permissions', []);
                    @endphp
                    
                    @foreach($permissions as $key => $label)
                        <div class="form-check">
                            <input type="checkbox" id="permission_{{ $key }}" name="permissions[]" 
                                   value="{{ $key }}" class="form-check-input"
                                   {{ in_array($key, $oldPermissions) ? 'checked' : '' }}>
                            <label for="permission_{{ $key }}" class="form-check-label">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    ✅ Tạo Admin User
                </button>
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">
                    ❌ Hủy bỏ
                </a>
            </div>
        </form>
    </div>

    <script>
        // Auto-select permissions based on role
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            
            if (role === 'admin') {
                // Admin gets most permissions
                const adminPermissions = [
                    'view_accounts', 'edit_accounts', 'ban_accounts',
                    'view_characters', 'edit_characters', 'delete_characters',
                    'manage_giftcodes', 'manage_coins', 'view_analytics',
                    'manage_ip_bans', 'view_logs'
                ];
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = adminPermissions.includes(checkbox.value);
                });
            } else if (role === 'moderator') {
                // Moderator gets limited permissions
                const moderatorPermissions = [
                    'view_accounts', 'view_characters', 'manage_giftcodes', 'view_logs'
                ];
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = moderatorPermissions.includes(checkbox.value);
                });
            } else {
                // Clear all permissions
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        });
    </script>
@endsection
