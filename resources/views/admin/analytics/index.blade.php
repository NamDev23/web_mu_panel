@extends('layouts.admin')

@section('title', 'Analytics Dashboard - MU Admin Panel')

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
        .period-selector {
            display: flex;
            gap: 10px;
        }
        .period-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }
        .period-btn.active, .period-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .stat-icon {
            font-size: 24px;
        }
        .stat-growth {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .growth-positive {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        .growth-negative {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            opacity: 0.8;
            font-size: 14px;
        }
        .stat-detail {
            font-size: 12px;
            opacity: 0.6;
            margin-top: 10px;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .chart-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
        }
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .top-performers {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }
        .performer-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            color: white;
        }
        .performer-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .performer-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .performer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .performer-item:last-child {
            border-bottom: none;
        }
        .performer-info {
            flex: 1;
        }
        .performer-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .performer-detail {
            font-size: 12px;
            opacity: 0.7;
        }
        .performer-value {
            font-weight: 600;
            text-align: right;
        }
        .export-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            margin-top: 30px;
            color: white;
        }
        .export-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .export-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .revenue-breakdown {
            margin-bottom: 30px;
        }
        .section-title {
            color: white;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .breakdown-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            color: white;
        }
        .breakdown-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .breakdown-icon {
            font-size: 24px;
        }
        .breakdown-header h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }
        .breakdown-stats {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .breakdown-label {
            font-size: 14px;
            opacity: 0.8;
        }
        .breakdown-value {
            font-weight: 600;
            font-size: 14px;
        }
        .breakdown-value.total {
            color: #10b981;
            font-size: 16px;
        }
        .breakdown-value.positive {
            color: #10b981;
        }
        .breakdown-value.negative {
            color: #ef4444;
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
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
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
        .no-data {
            text-align: center;
            padding: 40px 20px;
            opacity: 0.7;
        }
</style>
@endsection

@section('content')
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">📊 Analytics Dashboard</h1>
                <p class="page-subtitle">Thống kê và phân tích dữ liệu game</p>
            </div>
            <div class="period-selector">
                <a href="?period=7" class="period-btn {{ $period == '7' ? 'active' : '' }}">7 ngày</a>
                <a href="?period=30" class="period-btn {{ $period == '30' ? 'active' : '' }}">30 ngày</a>
                <a href="?period=90" class="period-btn {{ $period == '90' ? 'active' : '' }}">90 ngày</a>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <!-- Account Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">👥</span>
                    @if($stats['accounts']['growth'] != 0)
                        <span class="stat-growth {{ $stats['accounts']['growth'] > 0 ? 'growth-positive' : 'growth-negative' }}">
                            {{ $stats['accounts']['growth'] > 0 ? '+' : '' }}{{ number_format($stats['accounts']['growth'], 1) }}%
                        </span>
                    @endif
                </div>
                <div class="stat-value">{{ number_format($stats['accounts']['total']) }}</div>
                <div class="stat-label">Tổng tài khoản</div>
                <div class="stat-detail">
                    Mới: {{ number_format($stats['accounts']['new']) }} |
                    Hoạt động: {{ number_format($stats['accounts']['active']) }} |
                    Bị khóa: {{ number_format($stats['accounts']['banned']) }}
                </div>
            </div>

            <!-- Character Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">⚔️</span>
                </div>
                <div class="stat-value">{{ number_format($stats['characters']['total']) }}</div>
                <div class="stat-label">Tổng nhân vật</div>
                <div class="stat-detail">
                    Mới: {{ number_format($stats['characters']['new']) }} |
                    Hoạt động: {{ number_format($stats['characters']['active']) }}
                </div>
            </div>

            <!-- Revenue Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">💰</span>
                    @if($stats['revenue']['growth'] != 0)
                        <span class="stat-growth {{ $stats['revenue']['growth'] > 0 ? 'growth-positive' : 'growth-negative' }}">
                            {{ $stats['revenue']['growth'] > 0 ? '+' : '' }}{{ number_format($stats['revenue']['growth'], 1) }}%
                        </span>
                    @endif
                </div>
                <div class="stat-value">{{ number_format($stats['revenue']['period']) }}đ</div>
                <div class="stat-label">Doanh thu kỳ này</div>
                <div class="stat-detail">
                    {{ number_format($stats['revenue']['transactions']) }} giao dịch |
                    TB: {{ number_format($stats['revenue']['avg_value']) }}đ
                </div>
            </div>

            <!-- Giftcode Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">🎁</span>
                </div>
                <div class="stat-value">{{ number_format($stats['giftcodes']['total']) }}</div>
                <div class="stat-label">Tổng giftcode</div>
                <div class="stat-detail">
                    Hoạt động: {{ number_format($stats['giftcodes']['active']) }} |
                    Đã dùng: {{ number_format($stats['giftcodes']['usage']) }}
                </div>
            </div>

            <!-- Monthly Cards Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">🎫</span>
                </div>
                <div class="stat-value">{{ number_format($stats['monthly_cards']['total']) }}</div>
                <div class="stat-label">Monthly Cards</div>
                <div class="stat-detail">
                    Hoạt động: {{ number_format($stats['monthly_cards']['active']) }} |
                    Doanh thu: {{ number_format($stats['monthly_cards']['revenue']) }}đ
                </div>
            </div>

            <!-- Battle Pass Stats -->
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-icon">⚔️</span>
                </div>
                <div class="stat-value">{{ number_format($stats['battle_pass']['total']) }}</div>
                <div class="stat-label">Battle Pass</div>
                <div class="stat-detail">
                    Hoạt động: {{ number_format($stats['battle_pass']['active']) }} |
                    Doanh thu: {{ number_format($stats['battle_pass']['revenue']) }}đ
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="revenue-breakdown">
            <h3 class="section-title">💰 Chi tiết doanh thu</h3>
            <div class="breakdown-grid">
                <div class="breakdown-card">
                    <div class="breakdown-header">
                        <span class="breakdown-icon">💳</span>
                        <h4>Nạp Coin</h4>
                    </div>
                    <div class="breakdown-stats">
                        <div class="breakdown-item">
                            <span class="breakdown-label">Tổng:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['coin_recharge']['total']) }}đ</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Kỳ này:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['coin_recharge']['period']) }}đ</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Giao dịch:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['coin_recharge']['transactions']) }}</span>
                        </div>
                    </div>
                </div>

                <div class="breakdown-card">
                    <div class="breakdown-header">
                        <span class="breakdown-icon">🎫</span>
                        <h4>Monthly Cards & Battle Pass</h4>
                    </div>
                    <div class="breakdown-stats">
                        <div class="breakdown-item">
                            <span class="breakdown-label">Tổng:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['monthly_cards']['total']) }}đ</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Kỳ này:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['monthly_cards']['period']) }}đ</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Giao dịch:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['breakdown']['monthly_cards']['transactions']) }}</span>
                        </div>
                    </div>
                </div>

                <div class="breakdown-card">
                    <div class="breakdown-header">
                        <span class="breakdown-icon">📊</span>
                        <h4>Tổng kết</h4>
                    </div>
                    <div class="breakdown-stats">
                        <div class="breakdown-item">
                            <span class="breakdown-label">Tổng doanh thu:</span>
                            <span class="breakdown-value total">{{ number_format($stats['revenue']['total']) }}đ</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Tăng trưởng:</span>
                            <span class="breakdown-value {{ $stats['revenue']['growth'] >= 0 ? 'positive' : 'negative' }}">
                                {{ $stats['revenue']['growth'] >= 0 ? '+' : '' }}{{ number_format($stats['revenue']['growth'], 1) }}%
                            </span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">TB/giao dịch:</span>
                            <span class="breakdown-value">{{ number_format($stats['revenue']['avg_value']) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <!-- Registration Chart -->
            <div class="chart-card">
                <h3 class="chart-title">📈 Đăng ký theo ngày</h3>
                <div class="chart-container">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="chart-card">
                <h3 class="chart-title">💰 Doanh thu theo ngày</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Server Distribution -->
            <div class="chart-card">
                <h3 class="chart-title">🌐 Phân bố server</h3>
                <div class="chart-container">
                    <canvas id="serverChart"></canvas>
                </div>
            </div>

            <!-- Level Distribution -->
            <div class="chart-card">
                <h3 class="chart-title">⭐ Phân bố level</h3>
                <div class="chart-container">
                    <canvas id="levelChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="top-performers">
            <!-- Top Spenders -->
            <div class="performer-card">
                <h3 class="performer-title">💎 Top người nạp tiền</h3>
                <div class="performer-list">
                    @if(count($topData['spenders']) > 0)
                        @foreach($topData['spenders'] as $index => $spender)
                            <div class="performer-item">
                                <div class="performer-info">
                                    <div class="performer-name">{{ $spender->username }}</div>
                                    <div class="performer-detail">{{ $spender->transaction_count }} giao dịch</div>
                                </div>
                                <div class="performer-value">{{ number_format($spender->total_spent) }}đ</div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">Chưa có dữ liệu</div>
                    @endif
                </div>
            </div>

            <!-- Top Characters -->
            <div class="performer-card">
                <h3 class="performer-title">🏆 Top nhân vật</h3>
                <div class="performer-list">
                    @if(count($topData['characters']) > 0)
                        @foreach($topData['characters'] as $index => $character)
                            <div class="performer-item">
                                <div class="performer-info">
                                    <div class="performer-name">{{ $character->rname }}</div>
                                    <div class="performer-detail">{{ $character->username }} - Server {{ $character->serverid }}</div>
                                </div>
                                <div class="performer-value">Lv.{{ $character->level }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">Chưa có dữ liệu</div>
                    @endif
                </div>
            </div>

            <!-- Top Giftcodes -->
            <div class="performer-card">
                <h3 class="performer-title">🎁 Giftcode phổ biến</h3>
                <div class="performer-list">
                    @if(count($topData['giftcodes']) > 0)
                        @foreach($topData['giftcodes'] as $index => $giftcode)
                            <div class="performer-item">
                                <div class="performer-info">
                                    <div class="performer-name">{{ $giftcode->code }}</div>
                                    <div class="performer-detail">{{ $giftcode->name }}</div>
                                </div>
                                <div class="performer-value">{{ $giftcode->used_count }}/{{ $giftcode->max_uses }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">Chưa có dữ liệu</div>
                    @endif
                </div>
            </div>

            <!-- Top Monthly Cards & Battle Pass -->
            <div class="performer-card">
                <h3 class="performer-title">🎫 Monthly Cards & Battle Pass phổ biến</h3>
                <div class="performer-list">
                    @if(count($topData['monthly_cards']) > 0)
                        @foreach($topData['monthly_cards'] as $index => $card)
                            <div class="performer-item">
                                <div class="performer-info">
                                    <div class="performer-name">
                                        {{ $card->type == 'monthly_card' ? '🎫' : '⚔️' }} {{ $card->package_name }}
                                    </div>
                                    <div class="performer-detail">
                                        {{ $card->username }} ({{ $card->email }}) - {{ number_format($card->price) }}đ
                                    </div>
                                </div>
                                <div class="performer-value">{{ $card->purchase_count }} lần mua</div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">Chưa có dữ liệu</div>
                    @endif
                </div>
            </div>

            <!-- Recent Admin Actions -->
            <div class="performer-card">
                <h3 class="performer-title">📝 Hoạt động admin gần đây</h3>
                <div class="performer-list">
                    @if(count($topData['actions']) > 0)
                        @foreach($topData['actions'] as $action)
                            <div class="performer-item">
                                <div class="performer-info">
                                    <div class="performer-name">{{ $action->admin_username }}</div>
                                    <div class="performer-detail">
                                        {{ $action->action }} - {{ $action->target_name }}
                                        <br><small>{{ date('d/m/Y H:i', strtotime($action->created_at)) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">Chưa có dữ liệu</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <h3 class="export-title">📤 Xuất báo cáo</h3>
            <div class="export-buttons">
                <a href="{{ route('admin.analytics.export', ['type' => 'overview', 'period' => $period]) }}" class="btn btn-primary">
                    📊 Tổng quan
                </a>
                <a href="{{ route('admin.analytics.export', ['type' => 'accounts', 'period' => $period]) }}" class="btn btn-success">
                    👥 Tài khoản
                </a>
                <a href="{{ route('admin.analytics.export', ['type' => 'revenue', 'period' => $period]) }}" class="btn btn-warning">
                    💰 Doanh thu
                </a>
                <a href="{{ route('admin.analytics.export', ['type' => 'characters', 'period' => $period]) }}" class="btn btn-info">
                    ⚔️ Nhân vật
                </a>
                <a href="{{ route('admin.analytics.export', ['type' => 'monthly_cards', 'period' => $period]) }}" class="btn" style="background: linear-gradient(45deg, #8b5cf6, #7c3aed); color: white;">
                    🎫 Monthly Cards & Battle Pass
                </a>
            </div>
        </div>
    </div>

    <script>
        // Chart.js configuration
        Chart.defaults.color = '#ffffff';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // Registration Chart
        const registrationData = @json($chartData['registrations']);
        const registrationLabels = registrationData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
        });
        const registrationValues = registrationData.map(item => item.count);

        new Chart(document.getElementById('registrationChart'), {
            type: 'line',
            data: {
                labels: registrationLabels,
                datasets: [{
                    label: 'Đăng ký mới',
                    data: registrationValues,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Revenue Chart
        const revenueData = @json($chartData['revenues']);
        const revenueLabels = revenueData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
        });
        const revenueValues = revenueData.map(item => item.amount);

        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Server Distribution Chart
        const serverData = @json($chartData['servers']);
        const serverLabels = serverData.map(item => `Server ${item.serverid}`);
        const serverValues = serverData.map(item => item.count);

        new Chart(document.getElementById('serverChart'), {
            type: 'doughnut',
            data: {
                labels: serverLabels,
                datasets: [{
                    data: serverValues,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
                    ],
                    borderWidth: 2,
                    borderColor: 'rgba(255, 255, 255, 0.2)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Level Distribution Chart
        const levelData = @json($chartData['levels']);
        const levelLabels = levelData.map(item => `Level ${item.level_range}`);
        const levelValues = levelData.map(item => item.count);

        new Chart(document.getElementById('levelChart'), {
            type: 'pie',
            data: {
                labels: levelLabels,
                datasets: [{
                    data: levelValues,
                    backgroundColor: [
                        '#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: 'rgba(255, 255, 255, 0.2)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    </script>
@endsection

@section('scripts')
<script>
// Chart.js configuration
        Chart.defaults.color = '#ffffff';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // Registration Chart
        const registrationData = @json($chartData['registrations']);
        const registrationLabels = registrationData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
        });
        const registrationValues = registrationData.map(item => item.count);

        new Chart(document.getElementById('registrationChart'), {
            type: 'line',
            data: {
                labels: registrationLabels,
                datasets: [{
                    label: 'Đăng ký mới',
                    data: registrationValues,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Revenue Chart
        const revenueData = @json($chartData['revenues']);
        const revenueLabels = revenueData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
        });
        const revenueValues = revenueData.map(item => item.amount);

        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Server Distribution Chart
        const serverData = @json($chartData['servers']);
        const serverLabels = serverData.map(item => `Server ${item.serverid}`);
        const serverValues = serverData.map(item => item.count);

        new Chart(document.getElementById('serverChart'), {
            type: 'doughnut',
            data: {
                labels: serverLabels,
                datasets: [{
                    data: serverValues,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'
                    ],
                    borderWidth: 2,
                    borderColor: 'rgba(255, 255, 255, 0.2)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Level Distribution Chart
        const levelData = @json($chartData['levels']);
        const levelLabels = levelData.map(item => `Level ${item.level_range}`);
        const levelValues = levelData.map(item => item.count);

        new Chart(document.getElementById('levelChart'), {
            type: 'pie',
            data: {
                labels: levelLabels,
                datasets: [{
                    data: levelValues,
                    backgroundColor: [
                        '#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: 'rgba(255, 255, 255, 0.2)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
</script>
@endsection
