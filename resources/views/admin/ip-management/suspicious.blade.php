@extends('layouts.admin')

@section('title', 'IP đáng nghi - MU Admin Panel')

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
        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .nav-tab {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-tab.active, .nav-tab:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .threshold-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-bottom: 30px;
        }
        .threshold-form {
            display: flex;
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
            min-width: 150px;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
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
        .suspicious-table {
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
        .ip-address {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #f59e0b;
        }
        .risk-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .risk-low {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .risk-medium {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .risk-high {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border: 1px solid rgba(139, 92, 246, 0.3);
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .info-box {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-box h4 {
            margin-bottom: 8px;
            color: #3b82f6;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">⚠️ IP đáng nghi</h1>
            <p class="page-subtitle">Các IP có nhiều tài khoản truy cập (có thể là shared IP hoặc bot)</p>

            <div class="nav-tabs">
                <a href="{{ route('admin.ip-management.index') }}" class="nav-tab">📊 Tổng quan</a>
                <a href="{{ route('admin.ip-management.banned') }}" class="nav-tab">🚫 IP bị cấm</a>
                <a href="{{ route('admin.ip-management.suspicious') }}" class="nav-tab active">⚠️ IP đáng nghi</a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <h4>ℹ️ Thông tin về IP đáng nghi</h4>
            <p>IP được coi là đáng nghi khi có nhiều tài khoản khác nhau truy cập từ cùng một địa chỉ IP. Điều này có thể chỉ ra:</p>
            <ul style="margin-left: 20px; margin-top: 8px;">
                <li>Shared network (internet cafe, công ty, trường học)</li>
                <li>Bot hoặc automated accounts</li>
                <li>Account farming</li>
                <li>Proxy/VPN usage</li>
            </ul>
        </div>

        <!-- Threshold Section -->
        <div class="threshold-section">
            <form method="GET" action="{{ route('admin.ip-management.suspicious') }}" class="threshold-form">
                <div class="form-group">
                    <label>Ngưỡng đáng nghi</label>
                    <select name="threshold" class="form-control">
                        <option value="3" {{ $threshold == 3 ? 'selected' : '' }}>3+ tài khoản</option>
                        <option value="5" {{ $threshold == 5 ? 'selected' : '' }}>5+ tài khoản</option>
                        <option value="10" {{ $threshold == 10 ? 'selected' : '' }}>10+ tài khoản</option>
                        <option value="20" {{ $threshold == 20 ? 'selected' : '' }}>20+ tài khoản</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Lọc</button>
            </form>
        </div>

        <!-- Suspicious IPs Table -->
        <div class="suspicious-table">
            <div class="table-header">
                <h3 class="table-title">⚠️ Danh sách IP đáng nghi ({{ $suspiciousIps->total() }} records)</h3>
            </div>

            @if($suspiciousIps->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Số tài khoản</th>
                                <th>Tổng lượt truy cập</th>
                                <th>Hoạt động cuối</th>
                                <th>Mức độ rủi ro</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suspiciousIps as $suspiciousIp)
                                <tr>
                                    <td>
                                        <span class="ip-address">{{ $suspiciousIp->ip_address }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($suspiciousIp->user_count) }}</strong> tài khoản
                                    </td>
                                    <td>{{ number_format($suspiciousIp->login_count) }} lượt</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($suspiciousIp->last_activity)) }}</td>
                                    <td>
                                        @php
                                            $riskLevel = 'low';
                                            $riskText = 'Thấp';
                                            if ($suspiciousIp->user_count >= 20) {
                                                $riskLevel = 'high';
                                                $riskText = 'Cao';
                                            } elseif ($suspiciousIp->user_count >= 10) {
                                                $riskLevel = 'medium';
                                                $riskText = 'Trung bình';
                                            }
                                        @endphp
                                        <span class="risk-badge risk-{{ $riskLevel }}">{{ $riskText }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.ip-management.show', $suspiciousIp->ip_address) }}" class="btn btn-info btn-sm">👁️ Chi tiết</a>
                                            @if($suspiciousIp->user_count >= 10)
                                                <a href="{{ route('admin.ip-management.show', $suspiciousIp->ip_address) }}#ban-form" class="btn btn-warning btn-sm">⚠️ Cân nhắc cấm</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $suspiciousIps->appends(request()->query())->links('pagination.custom') }}
            @else
                <div class="no-data">
                    <h3>⚠️ Không có IP đáng nghi</h3>
                    <p>Không tìm thấy IP nào có từ {{ $threshold }} tài khoản trở lên</p>
                </div>
            @endif
        </div>
    </div>
@endsection
