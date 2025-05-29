@extends('layouts.user')

@section('title', 'Hồ sơ cá nhân - MU Game Portal')

@section('content')
<!-- Profile Header -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user"></i>
            Thông tin cá nhân
        </h3>
    </div>
    <div class="grid grid-2">
        <div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="font-weight: 500; color: #374151;">Tên đăng nhập:</label>
                    <div style="color: #6b7280; margin-top: 0.25rem;">{{ session('user_account.username') }}</div>
                </div>
                <div>
                    <label style="font-weight: 500; color: #374151;">Email:</label>
                    <div style="color: #6b7280; margin-top: 0.25rem;">{{ session('user_account.email') }}</div>
                </div>
                <div>
                    <label style="font-weight: 500; color: #374151;">Số điện thoại:</label>
                    <div style="color: #6b7280; margin-top: 0.25rem;">
                        {{ session('user_account.phone') ?: 'Chưa cập nhật' }}
                    </div>
                </div>
                <div>
                    <label style="font-weight: 500; color: #374151;">Trạng thái tài khoản:</label>
                    <div style="margin-top: 0.25rem;">
                        <span class="status-badge status-active">Hoạt động</span>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div style="text-align: center; padding: 2rem; background: #f9fafb; border-radius: 12px;">
                <div style="width: 80px; height: 80px; background: #667eea; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <i class="fas fa-user" style="font-size: 2rem; color: white;"></i>
                </div>
                <div style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                    {{ session('user_account.username') }}
                </div>
                <div style="color: #6b7280; font-size: 0.875rem;">
                    Thành viên từ {{ date('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Game Account Linking -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-gamepad"></i>
            Liên kết tài khoản game
        </h3>
    </div>
    @if(session('user_account.game_account_id'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.5rem;"></i>
                <div>
                    <div style="font-weight: 600; color: #166534;">Đã liên kết thành công</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Tài khoản game của bạn đã được liên kết</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.7); border-radius: 8px; padding: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="font-weight: 500; color: #374151;">Username game:</label>
                        <div style="color: #6b7280; margin-top: 0.25rem;">Loading...</div>
                    </div>
                    <div>
                        <label style="font-weight: 500; color: #374151;">Coin hiện tại:</label>
                        <div style="color: #f59e0b; font-weight: 600; margin-top: 0.25rem;">Loading...</div>
                    </div>
                    <div>
                        <label style="font-weight: 500; color: #374151;">Tổng nạp:</label>
                        <div style="color: #6b7280; margin-top: 0.25rem;">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
        <button onclick="unlinkGameAccount()" class="btn btn-danger">
            <i class="fas fa-unlink"></i>
            Hủy liên kết
        </button>
    @else
        <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 1.5rem;"></i>
                <div>
                    <div style="font-weight: 600; color: #92400e;">Chưa liên kết tài khoản game</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Liên kết để sử dụng đầy đủ tính năng</div>
                </div>
            </div>
        </div>
        
        <form method="POST" action="#" id="linkGameForm">
            @csrf
            <div class="form-group">
                <label for="game_username" class="form-label">Tên tài khoản game</label>
                <input 
                    type="text" 
                    name="game_username" 
                    id="game_username" 
                    class="form-input" 
                    placeholder="Nhập tên tài khoản game của bạn"
                    required
                >
                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                    Nhập chính xác tên tài khoản game để liên kết
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-link"></i>
                Liên kết tài khoản
            </button>
        </form>
    @endif
</div>

<!-- Security Settings -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-shield-alt"></i>
            Bảo mật tài khoản
        </h3>
    </div>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Change Password -->
        <div style="background: #f9fafb; border-radius: 8px; padding: 1.5rem;">
            <h4 style="margin-bottom: 1rem; color: #374151;">
                <i class="fas fa-key"></i> Đổi mật khẩu
            </h4>
            <form method="POST" action="#" id="changePasswordForm">
                @csrf
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input 
                            type="password" 
                            name="current_password" 
                            id="current_password" 
                            class="form-input" 
                            placeholder="Nhập mật khẩu hiện tại"
                            required
                        >
                    </div>
                    <div></div>
                    <div class="form-group">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input 
                            type="password" 
                            name="new_password" 
                            id="new_password" 
                            class="form-input" 
                            placeholder="Nhập mật khẩu mới"
                            required
                            minlength="6"
                        >
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                        <input 
                            type="password" 
                            name="new_password_confirmation" 
                            id="new_password_confirmation" 
                            class="form-input" 
                            placeholder="Nhập lại mật khẩu mới"
                            required
                        >
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i>
                    Cập nhật mật khẩu
                </button>
            </form>
        </div>

        <!-- Update Profile -->
        <div style="background: #f9fafb; border-radius: 8px; padding: 1.5rem;">
            <h4 style="margin-bottom: 1rem; color: #374151;">
                <i class="fas fa-edit"></i> Cập nhật thông tin
            </h4>
            <form method="POST" action="#" id="updateProfileForm">
                @csrf
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="form-input" 
                            value="{{ session('user_account.email') }}"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            class="form-input" 
                            value="{{ session('user_account.phone') }}"
                            placeholder="Nhập số điện thoại"
                        >
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    Cập nhật thông tin
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Account Statistics -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i>
            Thống kê tài khoản
        </h3>
    </div>
    <div class="grid grid-4">
        <div style="text-align: center; padding: 1.5rem; background: #f0f9ff; border-radius: 8px;">
            <i class="fas fa-coins" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">Loading...</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Coin hiện tại</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f0fdf4; border-radius: 8px;">
            <i class="fas fa-credit-card" style="font-size: 2rem; color: #10b981; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #166534;">Loading...</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Tổng nạp (VND)</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fef3c7; border-radius: 8px;">
            <i class="fas fa-exchange-alt" style="font-size: 2rem; color: #f59e0b; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #92400e;">Loading...</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Giao dịch</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fdf2f8; border-radius: 8px;">
            <i class="fas fa-gift" style="font-size: 2rem; color: #ec4899; margin-bottom: 1rem;"></i>
            <div style="font-size: 1.5rem; font-weight: 700; color: #be185d;">Loading...</div>
            <div style="color: #6b7280; font-size: 0.875rem;">Giftcode đã dùng</div>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="card" style="border: 2px solid #fecaca;">
    <div class="card-header">
        <h3 class="card-title" style="color: #dc2626;">
            <i class="fas fa-exclamation-triangle"></i>
            Vùng nguy hiểm
        </h3>
    </div>
    <div style="background: #fee2e2; border-radius: 8px; padding: 1.5rem;">
        <h4 style="color: #991b1b; margin-bottom: 1rem;">Xóa tài khoản</h4>
        <p style="color: #6b7280; margin-bottom: 1rem;">
            Xóa vĩnh viễn tài khoản và toàn bộ dữ liệu. Hành động này không thể hoàn tác.
        </p>
        <button onclick="deleteAccount()" class="btn btn-danger">
            <i class="fas fa-trash"></i>
            Xóa tài khoản
        </button>
    </div>
</div>
@endsection

@section('scripts')
// Form submissions
document.getElementById('linkGameForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('game_username').value;
    alert(`Chức năng liên kết tài khoản game "${username}" đang được phát triển!`);
});

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
        alert('Mật khẩu xác nhận không khớp!');
        return;
    }
    
    alert('Chức năng đổi mật khẩu đang được phát triển!');
});

document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Chức năng cập nhật thông tin đang được phát triển!');
});

function unlinkGameAccount() {
    if (confirm('Bạn có chắc chắn muốn hủy liên kết tài khoản game?')) {
        alert('Chức năng hủy liên kết đang được phát triển!');
    }
}

function deleteAccount() {
    if (confirm('Bạn có chắc chắn muốn xóa tài khoản? Hành động này không thể hoàn tác!')) {
        if (confirm('Xác nhận lần cuối: Xóa vĩnh viễn tài khoản?')) {
            alert('Chức năng xóa tài khoản đang được phát triển!');
        }
    }
}

// Load account statistics
function loadAccountStats() {
    // Mock data for now
    const stats = {
        coins: 125000,
        totalRecharged: 2500000,
        transactions: 15,
        giftcodesUsed: 8
    };
    
    // Update the display
    const statElements = document.querySelectorAll('[style*="font-size: 1.5rem"]');
    if (statElements.length >= 4) {
        statElements[0].textContent = new Intl.NumberFormat().format(stats.coins);
        statElements[1].textContent = new Intl.NumberFormat().format(stats.totalRecharged);
        statElements[2].textContent = stats.transactions;
        statElements[3].textContent = stats.giftcodesUsed;
    }
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadAccountStats);

// Password confirmation validation
const newPassword = document.getElementById('new_password');
const confirmPassword = document.getElementById('new_password_confirmation');

function validatePasswordMatch() {
    if (newPassword.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
    } else {
        confirmPassword.setCustomValidity('');
    }
}

newPassword.addEventListener('input', validatePasswordMatch);
confirmPassword.addEventListener('input', validatePasswordMatch);
@endsection
