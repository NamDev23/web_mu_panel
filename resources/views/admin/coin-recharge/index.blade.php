@extends('layouts.admin')

@section('title', 'Quản lý nạp coin - MU Admin Panel')

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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        .search-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
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
            min-width: 200px;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .recharge-table {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            position: relative;
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
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
        }
        .table-responsive::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        .scroll-indicator {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: rgba(59, 130, 246, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 5;
            animation: pulse 2s infinite;
            pointer-events: none;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }
        th {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
        }
        td {
            color: white;
            font-size: 14px;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .type-badge {
            background: rgba(107, 114, 128, 0.3);
            color: #d1d5db;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
        }
        .type-manual {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .amount-text {
            font-weight: 600;
            color: #10b981;
        }
        .coins-text {
            font-weight: 600;
            color: #f59e0b;
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
                padding: 20px;
            }
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            .form-control {
                min-width: auto;
                width: 100%;
            }
            .btn {
                width: 100%;
                margin-top: 10px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .table-responsive {
                max-height: 60vh;
            }
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
            .btn-sm {
                padding: 4px 8px;
                font-size: 10px;
            }
            .status-badge, .type-badge {
                font-size: 10px;
                padding: 4px 8px;
            }
            .amount-text, .coins-text {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .table-responsive {
                max-height: 50vh;
            }
            th, td {
                padding: 8px 6px;
                font-size: 11px;
            }
            .page-title {
                font-size: 22px;
            }
            .search-section {
                padding: 20px;
            }
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">💰 Quản lý nạp coin</h1>
                <p class="page-subtitle">Nạp coin thủ công và theo dõi lịch sử giao dịch</p>
            </div>
            <a href="{{ route('admin.coin-recharge.create') }}" class="btn btn-primary">➕ Nạp coin mới</a>
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
                <div class="stat-icon">📊</div>
                <div class="stat-value">{{ number_format($stats['today_total']) }}đ</div>
                <div class="stat-label">Tổng nạp hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔢</div>
                <div class="stat-value">{{ $stats['today_count'] }}</div>
                <div class="stat-label">Giao dịch hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-value">{{ number_format($stats['month_total']) }}đ</div>
                <div class="stat-label">Tổng nạp tháng này</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-value">{{ $stats['pending_count'] }}</div>
                <div class="stat-label">Giao dịch chờ xử lý</div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('admin.coin-recharge.index') }}" class="search-form">
                <div class="form-group">
                    <label>Loại tìm kiếm</label>
                    <select name="search_type" class="form-control">
                        <option value="username" {{ $searchType == 'username' ? 'selected' : '' }}>Tên tài khoản</option>
                        <option value="character_name" {{ $searchType == 'character_name' ? 'selected' : '' }}>Tên nhân vật</option>
                        <option value="transaction_id" {{ $searchType == 'transaction_id' ? 'selected' : '' }}>Mã giao dịch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="failed" {{ $statusFilter == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Loại giao dịch</label>
                    <select name="type" class="form-control">
                        <option value="all" {{ $typeFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="manual" {{ $typeFilter == 'manual' ? 'selected' : '' }}>Nạp thủ công</option>
                        <option value="card" {{ $typeFilter == 'card' ? 'selected' : '' }}>Thẻ cào</option>
                        <option value="bank" {{ $typeFilter == 'bank' ? 'selected' : '' }}>Chuyển khoản</option>
                        <option value="paypal" {{ $typeFilter == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- Recharge Table -->
        <div class="recharge-table">
            <div class="table-header">
                <h3 class="table-title">📋 Lịch sử nạp coin ({{ $recharges->total() }} giao dịch)</h3>
            </div>

            @if($recharges->count() > 0)
                <div class="table-responsive" id="tableContainer">
                    <div class="scroll-indicator" id="scrollIndicator">← Vuốt để xem thêm →</div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tài khoản</th>
                                <th>Nhân vật</th>
                                <th>Số tiền</th>
                                <th>Coin nhận</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th>Mã GD</th>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recharges as $recharge)
                                <tr>
                                    <td>{{ $recharge->id }}</td>
                                    <td>{{ $recharge->username }}</td>
                                    <td>{{ $recharge->character_name ?: 'N/A' }}</td>
                                    <td><span class="amount-text">{{ number_format($recharge->amount) }}đ</span></td>
                                    <td><span class="coins-text">{{ number_format($recharge->coins_added) }} coin</span></td>
                                    <td>
                                        <span class="type-badge {{ $recharge->type == 'manual' ? 'type-manual' : '' }}">
                                            {{ ucfirst($recharge->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $recharge->status }}">
                                            @switch($recharge->status)
                                                @case('completed')
                                                    Hoàn thành
                                                    @break
                                                @case('pending')
                                                    Chờ xử lý
                                                    @break
                                                @case('failed')
                                                    Thất bại
                                                    @break
                                                @default
                                                    {{ $recharge->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>{{ $recharge->transaction_id }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($recharge->created_at)) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.coin-recharge.show', $recharge->id) }}" class="btn btn-info btn-sm">👁️ Xem</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $recharges->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>💰 Chưa có giao dịch nạp coin nào</h3>
                    <p>Hãy thử thay đổi bộ lọc hoặc tạo giao dịch nạp coin mới</p>
                </div>
            @endif
        </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('tableContainer');
    const scrollIndicator = document.getElementById('scrollIndicator');

    if (tableContainer && scrollIndicator) {
        // Check if table needs horizontal scrolling
        function checkScrollNeeded() {
            const needsScroll = tableContainer.scrollWidth > tableContainer.clientWidth;
            if (needsScroll) {
                scrollIndicator.style.display = 'block';
            } else {
                scrollIndicator.style.display = 'none';
            }
        }

        // Hide indicator when user starts scrolling
        tableContainer.addEventListener('scroll', function() {
            if (tableContainer.scrollLeft > 10) {
                scrollIndicator.style.display = 'none';
            } else {
                checkScrollNeeded();
            }
        });

        // Check on load and resize
        checkScrollNeeded();
        window.addEventListener('resize', checkScrollNeeded);

        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (scrollIndicator.style.display !== 'none') {
                scrollIndicator.style.opacity = '0';
                setTimeout(function() {
                    scrollIndicator.style.display = 'none';
                }, 500);
            }
        }, 5000);
    }
});
</script>
@endsection
