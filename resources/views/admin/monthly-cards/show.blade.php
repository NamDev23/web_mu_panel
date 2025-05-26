@extends('layouts.admin')

@section('title', 'Chi tiết Monthly Card - MU Admin Panel')

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
            background: linear-gradient(45deg, #3b82f6, #2563eb);
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
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
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
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
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
            color: #3b82f6;
        }
        .reward-value {
            opacity: 0.9;
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
                <h1 class="page-title">🎫 Chi tiết Monthly Card</h1>
                <p class="page-subtitle">{{ $card->package_name }} - {{ $card->username }}</p>
            </div>
            <a href="{{ route('admin.monthly-cards.index') }}" class="btn btn-secondary">← Quay lại</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Card Info -->
            <div class="info-card">
                <h3 class="info-title">📋 Thông tin thẻ</h3>
                <div class="info-item">
                    <span class="info-label">Tên gói:</span>
                    <span class="info-value">{{ $card->package_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Giá:</span>
                    <span class="info-value">{{ number_format($card->price) }}đ</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Thời hạn:</span>
                    <span class="info-value">{{ $card->duration_days }} ngày</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Trạng thái:</span>
                    <span class="status-badge status-{{ $card->status }}">
                        @switch($card->status)
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
                                {{ $card->status }}
                        @endswitch
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ngày mua:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($card->purchased_at)) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hết hạn:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($card->expires_at)) }}</span>
                </div>
            </div>

            <!-- User Info -->
            <div class="info-card">
                <h3 class="info-title">👤 Thông tin người chơi</h3>
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value">{{ $card->username }}</span>
                </div>
                @if($card->email)
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $card->email }}</span>
                </div>
                @endif
                @if($card->full_name)
                <div class="info-item">
                    <span class="info-label">Tên đầy đủ:</span>
                    <span class="info-value">{{ $card->full_name }}</span>
                </div>
                @endif
                @if($card->vip_level)
                <div class="info-item">
                    <span class="info-label">VIP Level:</span>
                    <span class="info-value">{{ $card->vip_level }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Tạo bởi:</span>
                    <span class="info-value">{{ $card->created_by_username ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div class="rewards-section">
            <h3 class="rewards-title">🎁 Phần thưởng</h3>
            
            @php
                $dailyRewards = json_decode($card->daily_rewards, true);
            @endphp
            
            @if($dailyRewards)
                @if(isset($dailyRewards['daily_coins']))
                <div class="reward-item">
                    <div class="reward-label">💰 Coins hàng ngày</div>
                    <div class="reward-value">{{ number_format($dailyRewards['daily_coins']) }} coins/ngày</div>
                </div>
                @endif
                
                @if(isset($dailyRewards['bonus_coins']) && $dailyRewards['bonus_coins'])
                <div class="reward-item">
                    <div class="reward-label">💎 Bonus coins</div>
                    <div class="reward-value">{{ number_format($dailyRewards['bonus_coins']) }} coins (một lần)</div>
                </div>
                @endif
                
                @if(isset($dailyRewards['daily_items']) && $dailyRewards['daily_items'])
                <div class="reward-item">
                    <div class="reward-label">📦 Items hàng ngày</div>
                    <div class="reward-value">{{ $dailyRewards['daily_items'] }}</div>
                </div>
                @endif
            @endif
            
            @if($card->bonus_rewards)
            <div class="reward-item">
                <div class="reward-label">🎁 Bonus items</div>
                <div class="reward-value">{{ $card->bonus_rewards }}</div>
            </div>
            @endif
            
            @if($card->description)
            <div class="reward-item">
                <div class="reward-label">📝 Mô tả</div>
                <div class="reward-value">{{ $card->description }}</div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        @if($card->status == 'active')
        <div class="action-buttons">
            <button onclick="showExtendModal()" class="btn btn-primary">⏰ Gia hạn</button>
            <button onclick="showCancelModal()" class="btn" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">❌ Hủy thẻ</button>
        </div>
        @endif
    </div>

    <!-- Extend Modal -->
    <div id="extendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.1); backdrop-filter: blur(16px); border-radius: 15px; padding: 30px; color: white; min-width: 400px;">
            <h3 style="margin-bottom: 20px;">⏰ Gia hạn thẻ tháng</h3>
            <form action="{{ route('admin.monthly-cards.extend', $card->id) }}" method="POST">
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
            <h3 style="margin-bottom: 20px;">❌ Hủy thẻ tháng</h3>
            <form action="{{ route('admin.monthly-cards.cancel', $card->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Lý do hủy:</label>
                    <textarea name="reason" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); color: white;" placeholder="Nhập lý do hủy thẻ..." required></textarea>
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
