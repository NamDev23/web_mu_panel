@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản: {{ $account->username }} - MU Admin Panel')

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
        .account-header {
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
        .account-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .account-meta {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
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
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .action-buttons {
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
        }
        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 500;
            opacity: 0.8;
        }
        .info-value {
            font-weight: 600;
        }
        .characters-list {
            list-style: none;
        }
        .character-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .character-name {
            font-weight: 600;
            color: #3b82f6;
        }
        .character-details {
            font-size: 14px;
            opacity: 0.8;
        }
        .login-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-item:last-child {
            border-bottom: none;
        }
        .scrollable-content {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .scrollable-content::-webkit-scrollbar {
            width: 6px;
        }
        .scrollable-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        .scrollable-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
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
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            color: white;
            min-width: 400px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-secondary {
            background: rgba(107, 114, 128, 0.8);
            color: white;
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
            {{ $account->username }}
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Account Header -->
        <div class="account-header">
            <div class="account-info">
                <h1>👤 {{ $account->username }}</h1>
                <div class="account-meta">
                    <span class="status-badge {{ $account->status == 'active' ? 'status-active' : 'status-banned' }}">
                        {{ $account->status == 'active' ? 'Hoạt động' : 'Bị khóa' }}
                    </span>
                    <span class="vip-badge">VIP {{ $account->vip_level }}</span>
                    @if($account->status == 'banned' && $account->ban_reason)
                        <span style="color: #ef4444; font-size: 14px; margin-left: 10px;">
                            Lý do: {{ $account->ban_reason }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.accounts.edit', $account->id) }}" class="btn btn-primary">✏️ Chỉnh sửa</a>
                @if($account->status == 'active')
                    <button class="btn btn-danger" onclick="showBanModal()">🚫 Khóa tài khoản</button>
                @else
                    <form action="/admin/accounts/{{ $account->id }}/unban" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">✅ Mở khóa</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Basic Info -->
            <div class="info-card">
                <h3 class="card-title">📋 Thông tin cơ bản</h3>
                <div class="info-row">
                    <span class="info-label">ID tài khoản:</span>
                    <span class="info-value">{{ $account->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $account->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value">{{ $account->phone ?: 'Chưa cập nhật' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Họ và tên:</span>
                    <span class="info-value">{{ $account->full_name ?: 'Chưa cập nhật' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày đăng ký:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($account->created_at)) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lần đăng nhập cuối:</span>
                    <span class="info-value">{{ $account->last_login_at ? date('d/m/Y H:i', strtotime($account->last_login_at)) : 'Chưa đăng nhập' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IP cuối:</span>
                    <span class="info-value">{{ $account->last_login_ip ?: 'N/A' }}</span>
                </div>
            </div>

            <!-- Financial Info -->
            <div class="info-card">
                <h3 class="card-title">💰 Thông tin tài chính</h3>
                <div class="info-row">
                    <span class="info-label">Tổng số tiền nạp:</span>
                    <span class="info-value">{{ number_format($account->total_recharge) }}đ</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số dư hiện tại:</span>
                    <span class="info-value">{{ number_format($account->current_balance) }}đ</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cấp VIP:</span>
                    <span class="info-value">VIP {{ $account->vip_level }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số nhân vật:</span>
                    <span class="info-value">{{ $account->characters_count }} nhân vật</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái xác thực:</span>
                    <span class="info-value">{{ $account->is_verified ? '✅ Đã xác thực' : '❌ Chưa xác thực' }}</span>
                </div>
            </div>

            <!-- Characters -->
            <div class="info-card">
                <h3 class="card-title">⚔️ Danh sách nhân vật</h3>
                @if($account->characters_count > 0)
                    <div style="text-align: center; padding: 20px; opacity: 0.8;">
                        <p>📊 Tổng cộng: <strong>{{ $account->characters_count }} nhân vật</strong></p>
                        <p style="font-size: 14px; margin-top: 10px;">
                            💡 Tính năng xem chi tiết nhân vật sẽ được phát triển trong phiên bản tiếp theo
                        </p>
                    </div>
                @else
                    <div style="text-align: center; padding: 20px; opacity: 0.6;">
                        <p>Chưa có nhân vật nào</p>
                    </div>
                @endif
            </div>

            <!-- Recent Logins -->
            <div class="info-card">
                <h3 class="card-title">🌐 Lịch sử đăng nhập gần đây</h3>
                @if($recentLogins && count($recentLogins) > 0)
                    <div class="scrollable-content">
                        @foreach($recentLogins as $login)
                            <div class="login-item">
                                <span>{{ date('d/m/Y H:i', strtotime($login->created_at)) }}</span>
                                <span>{{ $login->ip_address }}</span>
                                <span style="font-size: 12px; opacity: 0.8;">{{ $login->action }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if(count($recentLogins) > 10)
                        <div style="text-align: center; margin-top: 10px; font-size: 12px; opacity: 0.7;">
                            📜 Hiển thị {{ count($recentLogins) }} logs gần nhất - Cuộn để xem thêm
                        </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 20px; opacity: 0.6;">
                        <p>Chưa có log đăng nhập</p>
                        <p style="font-size: 14px; margin-top: 5px;">
                            💡 Logs sẽ được ghi lại khi người chơi đăng nhập game
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ban Modal -->
    <div id="banModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 20px;">🚫 Khóa tài khoản</h3>
            <form action="/admin/accounts/{{ $account->id }}/ban" method="POST">
                @csrf
                <div class="form-group">
                    <label>Lý do khóa tài khoản:</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Nhập lý do khóa tài khoản..." required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="hideBanModal()">Hủy</button>
                    <button type="submit" class="btn btn-danger">Khóa tài khoản</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showBanModal() {
            document.getElementById('banModal').style.display = 'block';
        }

        function hideBanModal() {
            document.getElementById('banModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('banModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
@endsection

@section('scripts')
<script>
function showBanModal() {
            document.getElementById('banModal').style.display = 'block';
        }

        function hideBanModal() {
            document.getElementById('banModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('banModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
</script>
@endsection
