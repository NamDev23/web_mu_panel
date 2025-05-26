@extends('layouts.admin')

@section('title', 'Monthly Cards & Battle Pass - MU Admin Panel')

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
        .btn:hover {
            transform: translateY(-2px);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
            text-align: center;
        }
        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            opacity: 0.8;
            font-size: 14px;
        }
        .filters-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
        }
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group label {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        .form-control {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .cards-table {
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .type-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-monthly {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .type-battle {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
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
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        .user-details {
            flex: 1;
        }
        .user-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .user-email {
            font-size: 12px;
            opacity: 0.7;
        }
        .price-value {
            font-weight: 600;
            color: #10b981;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-info {
            background: linear-gradient(45deg, #06b6d4, #0891b2);
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: white;
            opacity: 0.7;
        }
        .no-data h3 {
            font-size: 18px;
            margin-bottom: 10px;
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
        .package-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .package-duration {
            font-size: 12px;
            opacity: 0.7;
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">🎫 Monthly Cards</h1>
                <p class="page-subtitle">Quản lý thẻ tháng với phần thưởng hàng ngày</p>
            </div>
            <a href="{{ route('admin.monthly-cards.create') }}" class="btn btn-primary">+ Tạo mới</a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎫</div>
                <div class="stat-value">{{ number_format($stats['total_cards']) }}</div>
                <div class="stat-label">Tổng thẻ</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ number_format($stats['active_cards']) }}</div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏰</div>
                <div class="stat-value">{{ number_format($stats['expired_cards']) }}</div>
                <div class="stat-label">Đã hết hạn</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">❌</div>
                <div class="stat-value">{{ number_format($stats['cancelled_cards']) }}</div>
                <div class="stat-label">Đã hủy</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">{{ number_format($stats['total_revenue']) }}đ</div>
                <div class="stat-label">Tổng doanh thu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-value">{{ number_format($stats['monthly_revenue']) }}đ</div>
                <div class="stat-label">Doanh thu tháng</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.monthly-cards.index') }}" class="filters-form">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Username, email, tên..." value="{{ $search }}">
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="expired" {{ $statusFilter == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">🔍 Lọc</button>
                </div>
            </form>
        </div>

        <!-- Cards Table -->
        <div class="cards-table">
            <div class="table-header">
                <h3 class="table-title">📋 Danh sách thẻ ({{ $monthlyCards->total() }} records)</h3>
            </div>

            @if($monthlyCards->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Người chơi</th>
                                <th>Gói thẻ tháng</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Ngày mua</th>
                                <th>Hết hạn</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyCards as $card)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($card->username, 0, 2)) }}
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">{{ $card->username }}</div>
                                                @if($card->email)
                                                    <div class="user-email">{{ $card->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="package-name">{{ $card->package_name }}</div>
                                        <div class="package-duration">{{ $card->duration_days }} ngày</div>
                                    </td>
                                    <td>
                                        <span class="price-value">{{ number_format($card->price) }}đ</span>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($card->purchased_at)) }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($card->expires_at)) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.monthly-cards.show', $card->id) }}" class="btn btn-info btn-sm">👁️ Chi tiết</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $monthlyCards->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>🎫 Chưa có thẻ nào</h3>
                    <p>Danh sách thẻ tháng và battle pass sẽ hiển thị ở đây</p>
                </div>
            @endif
        </div>
@endsection
