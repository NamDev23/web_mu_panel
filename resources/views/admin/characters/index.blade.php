@extends('layouts.admin')

@section('title', 'Quản lý nhân vật - MU Admin Panel')

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
        .characters-table {
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
        .character-name {
            font-weight: 600;
            color: #3b82f6;
        }
        .character-name:hover {
            text-decoration: underline;
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
        .status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .level-badge {
            background: linear-gradient(45deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .server-badge {
            background: rgba(107, 114, 128, 0.3);
            color: #d1d5db;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
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

        /* Mobile Responsive */
        @media (max-width: 768px) {
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
            .page-header {
                padding: 20px;
            }
            .page-title {
                font-size: 22px;
            }
            .search-section {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            .table-responsive {
                max-height: 50vh;
            }
            th, td {
                padding: 8px 6px;
                font-size: 11px;
            }
            .status-badge, .level-badge, .server-badge {
                font-size: 10px;
                padding: 2px 6px;
            }
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">⚔️ Quản lý nhân vật</h1>
            <p class="page-subtitle">Tìm kiếm, xem và quản lý thông tin nhân vật trong game</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('admin.characters.index') }}" class="search-form">
                <div class="form-group">
                    <label>Loại tìm kiếm</label>
                    <select name="search_type" class="form-control">
                        <option value="character_name" {{ $searchType == 'character_name' ? 'selected' : '' }}>Tên nhân vật</option>
                        <option value="username" {{ $searchType == 'username' ? 'selected' : '' }}>Tên tài khoản</option>
                        <option value="character_id" {{ $searchType == 'character_id' ? 'selected' : '' }}>ID nhân vật</option>
                        <option value="user_id" {{ $searchType == 'user_id' ? 'selected' : '' }}>ID tài khoản</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label>Server</label>
                    <select name="server" class="form-control">
                        <option value="all" {{ $serverFilter == 'all' ? 'selected' : '' }}>Tất cả server</option>
                        @foreach($servers as $server)
                            <option value="{{ $server->serverid }}" {{ $serverFilter == $server->serverid ? 'selected' : '' }}>
                                Server {{ $server->serverid }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- Characters Table -->
        <div class="characters-table">
            <div class="table-header">
                <h3 class="table-title">📋 Danh sách nhân vật ({{ $characters->total() }} kết quả)</h3>
            </div>

            @if($characters->count() > 0)
                <div class="table-responsive" id="tableContainer">
                    <div class="scroll-indicator" id="scrollIndicator">← Vuốt để xem thêm →</div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên nhân vật</th>
                                <th>Tài khoản</th>
                                <th>Level</th>
                                <th>Server</th>
                                <th>Nghề nghiệp</th>
                                <th>Tiền</th>
                                <th>Trạng thái</th>
                                <th>Đăng ký</th>
                                <th>Hoạt động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($characters as $character)
                                <tr>
                                    <td>{{ $character->rid }}</td>
                                    <td>
                                        <a href="{{ route('admin.characters.show', $character->rid) }}" class="character-name">
                                            {{ $character->character_name }}
                                        </a>
                                    </td>
                                    <td>{{ $character->username ?: 'N/A' }}</td>
                                    <td><span class="level-badge">Lv.{{ $character->level }}</span></td>
                                    <td><span class="server-badge">S{{ $character->serverid }}</span></td>
                                    <td>{{ $character->occupation }}</td>
                                    <td>{{ number_format($character->money) }}</td>
                                    <td>
                                        @if($character->isdel == 0)
                                            <span class="status-badge status-active">Hoạt động</span>
                                        @else
                                            <span class="status-badge status-banned">Bị khóa</span>
                                        @endif
                                    </td>
                                    <td>{{ $character->regtime ? date('d/m/Y', strtotime($character->regtime)) : 'N/A' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.characters.show', $character->rid) }}" class="btn btn-info btn-sm">👁️ Xem</a>
                                            <a href="{{ route('admin.characters.edit', $character->rid) }}" class="btn btn-warning btn-sm">✏️ Sửa</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $characters->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>🔍 Không tìm thấy nhân vật nào</h3>
                    <p>Hãy thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
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
