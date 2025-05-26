@extends('layouts.admin')

@section('title', 'Chi tiết nhân vật: {{ $character->rname }} - MU Admin Panel')

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
        .character-header {
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
        .character-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .character-meta {
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
        .level-badge {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .server-badge {
            background: linear-gradient(45deg, #06b6d4, #0891b2);
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
        .btn-primary {
            background: linear-gradient(45deg, #3b82f6, #2563eb);
            color: white;
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
        .login-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-item:last-child {
            border-bottom: none;
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
            <a href="/admin/characters">Quản lý nhân vật</a> /
            {{ $character->rname }}
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Character Header -->
        <div class="character-header">
            <div class="character-info">
                <h1>⚔️ {{ $character->rname }}</h1>
                <div class="character-meta">
                    <span class="status-badge {{ $character->isdel == 0 ? 'status-active' : 'status-banned' }}">
                        {{ $character->isdel == 0 ? 'Hoạt động' : 'Bị khóa' }}
                    </span>
                    <span class="level-badge">Level {{ $character->level }}</span>
                    <span class="server-badge">Server {{ $character->serverid }}</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.characters.edit', $character->rid) }}" class="btn btn-primary">✏️ Chỉnh sửa</a>
                @if($character->isdel == 0)
                    <button class="btn btn-danger" onclick="showBanModal()">🚫 Khóa nhân vật</button>
                @else
                    <form action="{{ route('admin.characters.unban', $character->rid) }}" method="POST" style="display: inline;">
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
                    <span class="info-label">ID nhân vật:</span>
                    <span class="info-value">{{ $character->rid }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên nhân vật:</span>
                    <span class="info-value">{{ $character->rname }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ID tài khoản:</span>
                    <span class="info-value">{{ $character->userid }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên tài khoản:</span>
                    <span class="info-value">{{ $character->username ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $character->email ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Server:</span>
                    <span class="info-value">Server {{ $character->serverid }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nghề nghiệp:</span>
                    <span class="info-value">{{ $character->occupation }}</span>
                </div>
            </div>

            <!-- Game Stats -->
            <div class="info-card">
                <h3 class="card-title">📊 Thống kê game</h3>
                <div class="info-row">
                    <span class="info-label">Level:</span>
                    <span class="info-value">{{ $character->level }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kinh nghiệm:</span>
                    <span class="info-value">{{ number_format($character->experience) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tiền trong game:</span>
                    <span class="info-value">{{ number_format($character->money) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày tạo:</span>
                    <span class="info-value">{{ $character->regtime ? date('d/m/Y H:i', strtotime($character->regtime)) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lần online cuối:</span>
                    <span class="info-value">{{ $character->lasttime ? date('d/m/Y H:i', strtotime($character->lasttime)) : 'Chưa online' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lần logout cuối:</span>
                    <span class="info-value">{{ $character->logofftime ? date('d/m/Y H:i', strtotime($character->logofftime)) : 'N/A' }}</span>
                </div>
            </div>

            <!-- Account Info -->
            <div class="info-card">
                <h3 class="card-title">👤 Thông tin tài khoản</h3>
                <div class="info-row">
                    <span class="info-label">Trạng thái tài khoản:</span>
                    <span class="info-value">
                        @if($character->account_status == 'active')
                            <span style="color: #10b981;">✅ Hoạt động</span>
                        @else
                            <span style="color: #ef4444;">❌ Bị khóa</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">VIP Level:</span>
                    <span class="info-value">VIP {{ $character->vip_level ?? 0 }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số dư tài khoản:</span>
                    <span class="info-value">{{ number_format($character->current_balance ?? 0) }}đ</span>
                </div>
            </div>

            <!-- Login History -->
            <div class="info-card">
                <h3 class="card-title">🌐 Lịch sử đăng nhập gần đây</h3>
                @if($loginHistory && count($loginHistory) > 0)
                    @foreach($loginHistory as $login)
                        <div class="login-item">
                            <span>{{ date('d/m/Y H:i', strtotime($login->created_at)) }}</span>
                            <span>{{ $login->ip_address }}</span>
                            <span style="font-size: 12px; opacity: 0.8;">{{ $login->action }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 20px; opacity: 0.6;">
                        <p>Chưa có log đăng nhập</p>
                        <p style="font-size: 14px; margin-top: 5px;">
                            💡 Logs sẽ được ghi lại khi nhân vật đăng nhập game
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Ban Modal -->
    <div id="banModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 20px;">🚫 Khóa nhân vật</h3>
            <form action="{{ route('admin.characters.ban', $character->rid) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Lý do khóa nhân vật:</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Nhập lý do khóa nhân vật..." required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="hideBanModal()">Hủy</button>
                    <button type="submit" class="btn btn-danger">Khóa nhân vật</button>
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
