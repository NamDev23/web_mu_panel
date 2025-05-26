@extends('layouts.admin')

@section('title', 'Chi tiết Battle Pass - MU Admin Panel')

@section('styles')
<style>
        .nav-links a:hover, .nav-links a.active {
            background: rgba(255, 255, 255, 0.1);
        }
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
        .page-subtitle {
            opacity: 0.8;
            font-size: 16px;
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
            font-size: 14px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 500;
            opacity: 0.8;
        }
        .info-value {
            font-weight: 600;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .status-expired {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .success-message {
            background: rgba(139, 92, 246, 0.2);
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .rewards-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
            color: white;
        }
        .rewards-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .reward-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .reward-item:last-child {
            margin-bottom: 0;
        }
        .reward-label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #8b5cf6;
        }
        .reward-value {
            opacity: 0.9;
        }
        .level-progress {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .progress-bar {
            background: rgba(255, 255, 255, 0.1);
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
            height: 100%;
            transition: width 0.3s ease;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">⚔️ Chi tiết Battle Pass</h1>
                <p class="page-subtitle">{{ $battlePass->package_name }} - {{ $battlePass->username }}</p>
            </div>
            <a href="{{ route('admin.battle-pass.index') }}" class="btn btn-secondary">← Quay lại</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Battle Pass Info -->
            <div class="info-card">
                <h3 class="info-title">⚔️ Thông tin Battle Pass</h3>
                <div class="info-item">
                    <span class="info-label">Season:</span>
                    <span class="info-value">{{ $battlePass->package_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Giá:</span>
                    <span class="info-value">{{ number_format($battlePass->price) }}đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Thời hạn:</span>
                    <span class="info-value">{{ $battlePass->duration_days }} ngày</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Trạng thái:</span>
                    <span class="status-badge status-{{ $battlePass->status }}">
                        @switch($battlePass->status)
                            @case('active')
                                Hoạt động
                                @break
                            @case('expired')
                                Hết hạn
                                @break
                            @case('cancelled')
                                Đã hủy
                                @break
                            @default
                                {{ $battlePass->status }}
                        @endswitch
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ngày mua:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($battlePass->purchased_at)) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hết hạn:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($battlePass->expires_at)) }}</span>
                </div>
            </div>

            <!-- User Info -->
            <div class="info-card">
                <h3 class="info-title">👤 Thông tin người chơi</h3>
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value">{{ $battlePass->username }}</span>
                </div>
                @if($battlePass->email)
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $battlePass->email }}</span>
                </div>
                @endif
                @if($battlePass->full_name)
                <div class="info-item">
                    <span class="info-label">Tên đầy đủ:</span>
                    <span class="info-value">{{ $battlePass->full_name }}</span>
                </div>
                @endif
                @if($battlePass->vip_level)
                <div class="info-item">
                    <span class="info-label">VIP Level:</span>
                    <span class="info-value">{{ $battlePass->vip_level }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Tạo bởi:</span>
                    <span class="info-value">{{ $battlePass->created_by_username ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div class="rewards-section">
            <h3 class="rewards-title">🏆 Hệ thống phần thưởng</h3>
            
            @php
                $battlePassData = json_decode($battlePass->daily_rewards, true);
            @endphp
            
            @if($battlePassData)
                @if(isset($battlePassData['max_level']))
                <div class="reward-item">
                    <div class="reward-label">🎯 Cấp độ tối đa</div>
                    <div class="reward-value">Level {{ $battlePassData['max_level'] }}</div>
                </div>
                @endif
                
                @if(isset($battlePassData['free_rewards']))
                <div class="reward-item">
                    <div class="reward-label">🆓 Phần thưởng miễn phí</div>
                    <div class="reward-value">{{ $battlePassData['free_rewards'] }}</div>
                </div>
                @endif
                
                @if(isset($battlePassData['premium_rewards']))
                <div class="reward-item">
                    <div class="reward-label">👑 Phần thưởng Premium</div>
                    <div class="reward-value">{{ $battlePassData['premium_rewards'] }}</div>
                </div>
                @endif
            @endif
            
            @if($battlePass->description)
            <div class="reward-item">
                <div class="reward-label">📝 Mô tả</div>
                <div class="reward-value">{{ $battlePass->description }}</div>
            </div>
            @endif

            <!-- Level Progress (Mock data for demo) -->
            @if($battlePass->status == 'active')
            <div class="level-progress">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 600;">📊 Tiến độ hiện tại</span>
                    <span style="opacity: 0.8;">Level 15 / {{ $battlePassData['max_level'] ?? 50 }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 30%;"></div>
                </div>
                <div style="font-size: 12px; opacity: 0.7; text-align: center; margin-top: 5px;">
                    3,500 / 5,000 XP để lên level tiếp theo
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        @if($battlePass->status == 'active')
        <div class="action-buttons">
            <button onclick="showExtendModal()" class="btn btn-primary">⏰ Gia hạn Season</button>
            <button onclick="showCancelModal()" class="btn" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">❌ Hủy Battle Pass</button>
        </div>
        @endif
    </div>

    <!-- Extend Modal -->
    <div id="extendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.1); backdrop-filter: blur(16px); border-radius: 15px; padding: 30px; color: white; min-width: 400px;">
            <h3 style="margin-bottom: 20px;">⏰ Gia hạn Battle Pass</h3>
            <form action="{{ route('admin.battle-pass.extend', $battlePass->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Số ngày gia hạn:</label>
                    <input type="number" name="extend_days" min="1" max="365" value="30" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Lý do:</label>
                    <textarea name="reason" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;" placeholder="Nhập lý do gia hạn..." required></textarea>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" onclick="hideExtendModal()" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gia hạn</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.1); backdrop-filter: blur(16px); border-radius: 15px; padding: 30px; color: white; min-width: 400px;">
            <h3 style="margin-bottom: 20px;">❌ Hủy Battle Pass</h3>
            <form action="{{ route('admin.battle-pass.cancel', $battlePass->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Lý do hủy:</label>
                    <textarea name="reason" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;" placeholder="Nhập lý do hủy battle pass..." required></textarea>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" onclick="hideCancelModal()" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showExtendModal() {
            document.getElementById('extendModal').style.display = 'block';
        }
        
        function hideExtendModal() {
            document.getElementById('extendModal').style.display = 'none';
        }
        
        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'block';
        }
        
        function hideCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }
    </script>
@endsection

@section('scripts')
<script>
function showExtendModal() {
            document.getElementById('extendModal').style.display = 'block';
        }
        
        function hideExtendModal() {
            document.getElementById('extendModal').style.display = 'none';
        }
        
        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'block';
        }
        
        function hideCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }
</script>
@endsection
