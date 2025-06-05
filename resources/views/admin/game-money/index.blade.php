@extends('layouts.admin')

@section('title', 'Quản lý xu game - MU Admin Panel')

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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .page-desc {
        opacity: 0.9;
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
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        color: white;
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 14px;
    }
    .form-control {
        padding: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    .btn {
        padding: 10px 20px;
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
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .money-table {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }
    .table-header {
        padding: 20px 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .table-title {
        color: white;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    .table-responsive {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
    }
    th {
        background: rgba(255, 255, 255, 0.1);
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
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
    .money-value {
        font-weight: 600;
        color: #fbbf24;
    }
    .realmoney-value {
        font-weight: 600;
        color: #3b82f6;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
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
    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: white;
    }
    .no-data h3 {
        font-size: 24px;
        margin-bottom: 10px;
        opacity: 0.8;
    }
    .no-data p {
        opacity: 0.6;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        .search-form {
            grid-template-columns: 1fr;
        }
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">💰 Quản lý xu game</h1>
            <p class="page-desc">Quản lý RealMoney và Money của người chơi</p>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <form class="search-form" method="GET">
            <div class="form-group">
                <label>Loại tìm kiếm</label>
                <select name="search_type" class="form-control">
                    <option value="username" {{ $searchType == 'username' ? 'selected' : '' }}>Tên tài khoản</option>
                    <option value="email" {{ $searchType == 'email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>
            <div class="form-group">
                <label>Từ khóa</label>
                <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="{{ $search }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
            </div>
        </form>
    </div>

    <!-- Money Table -->
    <div class="money-table">
        <div class="table-header">
            <h3 class="table-title">💰 Danh sách tài khoản ({{ $accounts->total() }} tài khoản)</h3>
        </div>

        @if($accounts->count() > 0)
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên tài khoản</th>
                            <th>Email</th>
                            <th>RealMoney</th>
                            <th>Money (Zen)</th>
                            <th>Tổng</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>{{ $account->ID }}</td>
                                <td><strong>{{ $account->UserName }}</strong></td>
                                <td>{{ $account->Email ?: 'N/A' }}</td>
                                <td><span class="realmoney-value">{{ number_format($account->yuanbao) }}</span></td>
                                <td><span class="money-value">{{ number_format($account->money) }}</span></td>
                                <td><strong>{{ number_format($account->yuanbao + $account->money) }}</strong></td>
                                <td>
                                    <span class="status-badge {{ $account->Status == 1 ? 'status-active' : 'status-banned' }}">
                                        {{ $account->Status == 1 ? 'Hoạt động' : 'Bị khóa' }}
                                    </span>
                                </td>
                                <td>{{ $account->CreateTime ? date('d/m/Y', strtotime($account->CreateTime)) : 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.game-money.show', $account->ID) }}" class="btn btn-info btn-sm">👁️ Xem</a>
                                        <a href="{{ route('admin.game-money.edit', $account->ID) }}" class="btn btn-warning btn-sm">✏️ Sửa</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            {{ $accounts->appends(request()->query())->links('pagination.custom') }}
        @else
            <div class="no-data">
                <h3>💰 Không tìm thấy tài khoản nào</h3>
                <p>Hãy thử thay đổi từ khóa tìm kiếm</p>
            </div>
        @endif
    </div>
@endsection
