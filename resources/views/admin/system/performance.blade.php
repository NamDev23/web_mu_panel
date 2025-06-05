@extends('layouts.admin')

@section('title', 'Hiệu suất hệ thống - MU Admin Panel')

@section('styles')
<style>
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
    .page-desc {
        opacity: 0.9;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
    }
    .stat-card h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .stat-item:last-child {
        border-bottom: none;
    }
    .stat-label {
        font-weight: 500;
        opacity: 0.8;
    }
    .stat-value {
        font-weight: 600;
    }
    .status-good {
        color: #10b981;
    }
    .status-warning {
        color: #f59e0b;
    }
    .status-error {
        color: #ef4444;
    }
    .actions-section {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 25px;
        color: white;
        margin-bottom: 30px;
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
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .btn-danger {
        background: linear-gradient(45deg, #ef4444, #dc2626);
        color: white;
    }
    .btn-warning {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        color: white;
    }
    .btn-info {
        background: linear-gradient(45deg, #06b6d4, #0891b2);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
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
    .table-section {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 25px;
        color: white;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .table th {
        background: rgba(255, 255, 255, 0.1);
        font-weight: 600;
    }
    .table tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    @if(session('success'))
        <div class="success-message">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">⚡ Hiệu suất hệ thống</h1>
        <p class="page-desc">Theo dõi hiệu suất database, cache và hệ thống</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Website Database -->
        <div class="stat-card">
            <h3>🗄️ Website Database</h3>
            <div class="stat-item">
                <span class="stat-label">Kết nối</span>
                <span class="stat-value {{ $stats['website_db']['connection'] === 'Connected' ? 'status-good' : 'status-error' }}">
                    {{ $stats['website_db']['connection'] }}
                </span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Thời gian truy vấn</span>
                <span class="stat-value">{{ $stats['website_db']['query_time'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Tổng records</span>
                <span class="stat-value">{{ number_format($stats['website_db']['total_rows']) }}</span>
            </div>
            @if(isset($stats['website_db']['tables']))
                @foreach($stats['website_db']['tables'] as $table => $count)
                    <div class="stat-item">
                        <span class="stat-label">{{ $table }}</span>
                        <span class="stat-value">{{ is_numeric($count) ? number_format($count) : $count }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Game Database -->
        <div class="stat-card">
            <h3>🎮 Game Database</h3>
            <div class="stat-item">
                <span class="stat-label">Kết nối</span>
                <span class="stat-value {{ $stats['game_db']['connection'] === 'Connected' ? 'status-good' : 'status-error' }}">
                    {{ $stats['game_db']['connection'] }}
                </span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Thời gian truy vấn</span>
                <span class="stat-value">{{ $stats['game_db']['query_time'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Tổng records</span>
                <span class="stat-value">{{ number_format($stats['game_db']['total_rows']) }}</span>
            </div>
            @if(isset($stats['game_db']['tables']))
                @foreach($stats['game_db']['tables'] as $table => $count)
                    <div class="stat-item">
                        <span class="stat-label">{{ $table }}</span>
                        <span class="stat-value">{{ is_numeric($count) ? number_format($count) : $count }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Cache Stats -->
        <div class="stat-card">
            <h3>💾 Cache System</h3>
            <div class="stat-item">
                <span class="stat-label">Driver</span>
                <span class="stat-value">{{ $stats['cache_stats']['driver'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Trạng thái</span>
                <span class="stat-value {{ $stats['cache_stats']['status'] === 'Active' ? 'status-good' : 'status-error' }}">
                    {{ $stats['cache_stats']['status'] }}
                </span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Thời gian phản hồi</span>
                <span class="stat-value">{{ $stats['cache_stats']['response_time'] }}</span>
            </div>
        </div>

        <!-- System Info -->
        <div class="stat-card">
            <h3>🖥️ System Info</h3>
            <div class="stat-item">
                <span class="stat-label">PHP Version</span>
                <span class="stat-value">{{ $stats['system_info']['php_version'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Laravel Version</span>
                <span class="stat-value">{{ $stats['system_info']['laravel_version'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Memory Usage</span>
                <span class="stat-value">{{ $stats['system_info']['memory_usage'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Memory Peak</span>
                <span class="stat-value">{{ $stats['system_info']['memory_peak'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Server Time</span>
                <span class="stat-value">{{ $stats['system_info']['server_time'] }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Timezone</span>
                <span class="stat-value">{{ $stats['system_info']['timezone'] }}</span>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="actions-section">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">🔧 Hành động hệ thống</h3>
        <p style="opacity: 0.8; margin-bottom: 20px;">Các thao tác bảo trì và tối ưu hóa hệ thống</p>
        
        <form action="{{ route('admin.system.clear-cache') }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="type" value="game_data">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Bạn có chắc chắn muốn xóa cache dữ liệu game?')">
                🗑️ Xóa Cache Game Data
            </button>
        </form>

        <form action="{{ route('admin.system.clear-cache') }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="type" value="all">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ cache?')">
                🗑️ Xóa Toàn Bộ Cache
            </button>
        </form>

        <a href="{{ route('admin.system.logs') }}" class="btn btn-info">
            📋 Xem Admin Logs
        </a>
    </div>
@endsection
