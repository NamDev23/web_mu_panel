@extends('layouts.admin')

@section('title', 'Quản lý giftcode - MU Admin Panel')

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
        .giftcode-table {
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
        .giftcode-code {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #3b82f6;
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
        .usage-progress {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 5px;
        }
        .usage-bar {
            height: 100%;
            background: linear-gradient(45deg, #10b981, #059669);
            transition: width 0.3s;
        }
        .usage-text {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 2px;
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
        .rewards-preview {
            font-size: 12px;
            opacity: 0.8;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">🎁 Quản lý giftcode</h1>
                <p class="page-subtitle">Tạo và quản lý giftcode cho người chơi</p>
            </div>
            <a href="{{ route('admin.giftcodes.create') }}" class="btn btn-primary">➕ Tạo giftcode mới</a>
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
                <div class="stat-value">{{ $stats['total_giftcodes'] }}</div>
                <div class="stat-label">Tổng giftcode</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value">{{ $stats['active_giftcodes'] }}</div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏰</div>
                <div class="stat-value">{{ $stats['expired_giftcodes'] }}</div>
                <div class="stat-label">Đã hết hạn</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-value">{{ $stats['total_usage'] }}</div>
                <div class="stat-label">Lượt sử dụng</div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('admin.giftcodes.index') }}" class="search-form">
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo code, tên, mô tả..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="expired" {{ $statusFilter == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Vô hiệu hóa</option>
                        <option value="used_up" {{ $statusFilter == 'used_up' ? 'selected' : '' }}>Đã hết lượt</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- Giftcode Table -->
        <div class="giftcode-table">
            <div class="table-header">
                <h3 class="table-title">📋 Danh sách giftcode ({{ $giftcodes->total() }} giftcode)</h3>
            </div>

            @if($giftcodes->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Tên</th>
                                <th>Phần thưởng</th>
                                <th>Sử dụng</th>
                                <th>Trạng thái</th>
                                <th>Hết hạn</th>
                                <th>Tạo bởi</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($giftcodes as $giftcode)
                                @php
                                    $rewards = json_decode($giftcode->rewards, true);
                                    $isExpired = $giftcode->expires_at && now() > $giftcode->expires_at;
                                    $isUsedUp = $giftcode->used_count >= $giftcode->max_uses;
                                    $usagePercent = $giftcode->max_uses > 0 ? ($giftcode->used_count / $giftcode->max_uses) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="giftcode-code">{{ $giftcode->code }}</span>
                                    </td>
                                    <td>{{ $giftcode->name }}</td>
                                    <td>
                                        <div class="rewards-preview">
                                            @if(isset($rewards['coins']) && $rewards['coins'] > 0)
                                                💰 {{ number_format($rewards['coins']) }} coin
                                            @endif
                                            @if(isset($rewards['items']) && count($rewards['items']) > 0)
                                                🎁 {{ count($rewards['items']) }} item(s)
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $giftcode->used_count }}/{{ $giftcode->max_uses }}</div>
                                        <div class="usage-progress">
                                            <div class="usage-bar" style="width: {{ $usagePercent }}%"></div>
                                        </div>
                                        <div class="usage-text">{{ number_format($usagePercent, 1) }}%</div>
                                    </td>
                                    <td>
                                        @if(!$giftcode->is_active)
                                            <span class="status-badge status-inactive">Vô hiệu hóa</span>
                                        @elseif($isExpired)
                                            <span class="status-badge status-expired">Hết hạn</span>
                                        @elseif($isUsedUp)
                                            <span class="status-badge status-used-up">Hết lượt</span>
                                        @else
                                            <span class="status-badge status-active">Hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $giftcode->expires_at ? date('d/m/Y', strtotime($giftcode->expires_at)) : 'Không giới hạn' }}
                                    </td>
                                    <td>{{ $giftcode->admin_username }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($giftcode->created_at)) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.giftcodes.show', $giftcode->id) }}" class="btn btn-info btn-sm">👁️ Xem</a>
                                            <a href="{{ route('admin.giftcodes.edit', $giftcode->id) }}" class="btn btn-warning btn-sm">✏️ Sửa</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $giftcodes->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>🎁 Chưa có giftcode nào</h3>
                    <p>Hãy tạo giftcode đầu tiên cho người chơi</p>
                </div>
            @endif
        </div>
@endsection
