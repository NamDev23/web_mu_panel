@extends('layouts.admin')

@section('title', 'Chi tiết giftcode: {{ $giftcode->code }} - MU Admin Panel')

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
        .giftcode-header {
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
        .giftcode-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .giftcode-code {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 15px;
            display: inline-block;
        }
        .giftcode-meta {
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
        .status-expired {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status-inactive {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }
        .status-used-up {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .usage-badge {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
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
        .btn-secondary {
            background: rgba(107, 114, 128, 0.8);
            color: white;
        }
        .btn-warning {
            background: linear-gradient(45deg, #f59e0b, #d97706);
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
        .rewards-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }
        .rewards-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #f59e0b;
        }
        .reward-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .reward-item:last-child {
            border-bottom: none;
        }
        .usage-progress {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: 10px 0;
        }
        .usage-bar {
            height: 100%;
            background: linear-gradient(45deg, #10b981, #059669);
            transition: width 0.3s;
        }
        .usage-text {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
        }
        .usage-history {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        .table-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .table-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            color: white;
            font-size: 14px;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: white;
            opacity: 0.7;
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
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/admin/dashboard">Dashboard</a> /
            <a href="/admin/giftcodes">Quản lý giftcode</a> /
            {{ $giftcode->code }}
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Giftcode Header -->
        <div class="giftcode-header">
            <div class="giftcode-info">
                <h1>🎁 {{ $giftcode->name }}</h1>
                <div class="giftcode-code">{{ $giftcode->code }}</div>
                <div class="giftcode-meta">
                    @php
                        $isExpired = $giftcode->expires_at && now() > $giftcode->expires_at;
                        $isUsedUp = $giftcode->used_count >= $giftcode->max_uses;
                        $usagePercent = $giftcode->max_uses > 0 ? ($giftcode->used_count / $giftcode->max_uses) * 100 : 0;
                    @endphp
                    
                    @if(!$giftcode->is_active)
                        <span class="status-badge status-inactive">Vô hiệu hóa</span>
                    @elseif($isExpired)
                        <span class="status-badge status-expired">Hết hạn</span>
                    @elseif($isUsedUp)
                        <span class="status-badge status-used-up">Hết lượt</span>
                    @else
                        <span class="status-badge status-active">Hoạt động</span>
                    @endif
                    
                    <span class="usage-badge">{{ $giftcode->used_count }}/{{ $giftcode->max_uses }} lượt</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.giftcodes.edit', $giftcode->id) }}" class="btn btn-warning">✏️ Chỉnh sửa</a>
                <a href="{{ route('admin.giftcodes.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Basic Information -->
            <div class="info-card">
                <h3 class="card-title">📋 Thông tin cơ bản</h3>
                <div class="info-row">
                    <span class="info-label">Mã giftcode:</span>
                    <span class="info-value">{{ $giftcode->code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên:</span>
                    <span class="info-value">{{ $giftcode->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mô tả:</span>
                    <span class="info-value">{{ $giftcode->description ?: 'Không có' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="info-value">
                        @if(!$giftcode->is_active)
                            ❌ Vô hiệu hóa
                        @elseif($isExpired)
                            ⏰ Hết hạn
                        @elseif($isUsedUp)
                            🔄 Hết lượt
                        @else
                            ✅ Hoạt động
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày tạo:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($giftcode->created_at)) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tạo bởi:</span>
                    <span class="info-value">{{ $giftcode->admin_username }}</span>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="info-card">
                <h3 class="card-title">📊 Thống kê sử dụng</h3>
                <div class="info-row">
                    <span class="info-label">Số lần sử dụng:</span>
                    <span class="info-value">{{ $giftcode->used_count }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Giới hạn sử dụng:</span>
                    <span class="info-value">{{ $giftcode->max_uses }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Còn lại:</span>
                    <span class="info-value">{{ $giftcode->max_uses - $giftcode->used_count }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tỷ lệ sử dụng:</span>
                    <span class="info-value">{{ number_format($usagePercent, 1) }}%</span>
                </div>
                <div class="usage-progress">
                    <div class="usage-bar" style="width: {{ $usagePercent }}%"></div>
                </div>
                <div class="usage-text">{{ $giftcode->used_count }} / {{ $giftcode->max_uses }} lượt đã sử dụng</div>
                
                <div class="info-row" style="margin-top: 15px;">
                    <span class="info-label">Hết hạn:</span>
                    <span class="info-value">
                        {{ $giftcode->expires_at ? date('d/m/Y H:i', strtotime($giftcode->expires_at)) : 'Không giới hạn' }}
                    </span>
                </div>
            </div>

            <!-- Rewards Information -->
            <div class="info-card">
                <h3 class="card-title">🎁 Phần thưởng</h3>
                <div class="rewards-section">
                    <div class="rewards-title">💰 Phần thưởng khi sử dụng</div>
                    @if(isset($giftcode->rewards['coins']) && $giftcode->rewards['coins'] > 0)
                        <div class="reward-item">
                            <span>💰 Coin:</span>
                            <span>{{ number_format($giftcode->rewards['coins']) }}</span>
                        </div>
                    @endif
                    
                    @if(isset($giftcode->rewards['items']) && count($giftcode->rewards['items']) > 0)
                        @foreach($giftcode->rewards['items'] as $item)
                            <div class="reward-item">
                                <span>🎁 {{ $item['name'] }}:</span>
                                <span>{{ $item['quantity'] }}</span>
                            </div>
                        @endforeach
                    @endif
                    
                    @if((!isset($giftcode->rewards['coins']) || $giftcode->rewards['coins'] == 0) && 
                        (!isset($giftcode->rewards['items']) || count($giftcode->rewards['items']) == 0))
                        <div style="text-align: center; opacity: 0.7; padding: 20px;">
                            Không có phần thưởng
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usage History -->
        <div class="usage-history">
            <div class="table-header">
                <h3 class="table-title">📋 Lịch sử sử dụng ({{ count($usageHistory) }} lượt gần đây)</h3>
            </div>
            
            @if(count($usageHistory) > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tài khoản</th>
                                <th>Nhân vật</th>
                                <th>Thời gian sử dụng</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageHistory as $usage)
                                <tr>
                                    <td>{{ $usage->username }}</td>
                                    <td>{{ $usage->character_name ?: 'N/A' }}</td>
                                    <td>{{ date('d/m/Y H:i:s', strtotime($usage->used_at)) }}</td>
                                    <td>{{ $usage->ip_address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-data">
                    <h3>📋 Chưa có ai sử dụng giftcode này</h3>
                    <p>Lịch sử sử dụng sẽ hiển thị ở đây khi có người sử dụng</p>
                </div>
            @endif
        </div>
    </div>
@endsection
