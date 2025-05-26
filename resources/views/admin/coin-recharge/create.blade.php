@extends('layouts.admin')

@section('title', 'Nạp coin thủ công - MU Admin Panel')

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
        .recharge-form {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            color: white;
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .form-header p {
            opacity: 0.8;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group label {
            font-weight: 600;
            font-size: 14px;
            color: white;
        }
        .form-control {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .account-search {
            position: relative;
        }
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            margin-top: 5px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .search-result-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .search-result-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .account-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            display: none;
        }
        .account-info h3 {
            margin-bottom: 15px;
            color: #3b82f6;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
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
        .amount-calculator {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .calculator-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #f59e0b;
        }
        .quick-amounts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .quick-amount-btn {
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .quick-amount-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .conversion-info {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
        }
        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
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
            background: rgba(107, 114, 128, 0.8);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .warning-box {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            color: #fbbf24;
        }
        .warning-box h4 {
            margin-bottom: 8px;
        }
        .alert alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
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
            <a href="/admin/coin-recharge">Quản lý nạp coin</a> /
            Nạp coin mới
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-error">
                <h4>❌ Có lỗi xảy ra:</h4>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Recharge Form -->
        <div class="recharge-form">
            <div class="form-header">
                <h1>💰 Nạp coin thủ công</h1>
                <p>Nạp coin trực tiếp vào tài khoản người chơi</p>
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <h4>⚠️ Lưu ý quan trọng</h4>
                <p>Việc nạp coin thủ công sẽ được ghi lại đầy đủ trong hệ thống. Hãy kiểm tra kỹ thông tin trước khi thực hiện.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.coin-recharge.store') }}" method="POST" id="rechargeForm">
                @csrf
                <div class="form-grid">
                    <!-- Account Search -->
                    <div class="form-group">
                        <label for="username">Tên tài khoản *</label>
                        <div class="account-search">
                            <input type="text" id="username" name="username" class="form-control" 
                                   placeholder="Nhập tên tài khoản..." 
                                   value="{{ old('username') }}" required autocomplete="off">
                            <div class="search-results" id="searchResults"></div>
                        </div>
                        <small style="opacity: 0.7; font-size: 12px;">Nhập tên tài khoản để tìm kiếm</small>
                    </div>

                    <!-- Account Info Display -->
                    <div class="account-info" id="accountInfo">
                        <h3>📋 Thông tin tài khoản</h3>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value" id="accountEmail">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Số dư hiện tại:</span>
                            <span class="info-value" id="accountBalance">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">VIP Level:</span>
                            <span class="info-value" id="accountVip">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Trạng thái:</span>
                            <span class="info-value" id="accountStatus">-</span>
                        </div>
                    </div>

                    <!-- Amount Calculator -->
                    <div class="amount-calculator">
                        <div class="calculator-title">💵 Tính toán số tiền</div>
                        <div class="quick-amounts">
                            <div class="quick-amount-btn" onclick="setAmount(50000, 50000)">50,000đ</div>
                            <div class="quick-amount-btn" onclick="setAmount(100000, 100000)">100,000đ</div>
                            <div class="quick-amount-btn" onclick="setAmount(200000, 200000)">200,000đ</div>
                            <div class="quick-amount-btn" onclick="setAmount(500000, 500000)">500,000đ</div>
                            <div class="quick-amount-btn" onclick="setAmount(1000000, 1000000)">1,000,000đ</div>
                            <div class="quick-amount-btn" onclick="setAmount(2000000, 2000000)">2,000,000đ</div>
                        </div>
                        <div class="conversion-info">
                            💡 Tỷ lệ chuyển đổi: 1đ = 1 coin
                        </div>
                    </div>

                    <!-- Amount Input -->
                    <div class="form-group">
                        <label for="amount">Số tiền nạp (VNĐ) *</label>
                        <input type="number" id="amount" name="amount" class="form-control" 
                               placeholder="Nhập số tiền..." 
                               value="{{ old('amount') }}" 
                               min="1000" max="100000000" required>
                        <small style="opacity: 0.7; font-size: 12px;">Tối thiểu 1,000đ - Tối đa 100,000,000đ</small>
                    </div>

                    <!-- Coins Added -->
                    <div class="form-group">
                        <label for="coins_added">Số coin nhận được *</label>
                        <input type="number" id="coins_added" name="coins_added" class="form-control" 
                               placeholder="Số coin sẽ được cộng vào tài khoản..." 
                               value="{{ old('coins_added') }}" 
                               min="1" max="1000000" required>
                        <small style="opacity: 0.7; font-size: 12px;">Số coin thực tế sẽ được cộng vào tài khoản</small>
                    </div>

                    <!-- Character Name (Optional) -->
                    <div class="form-group">
                        <label for="character_name">Tên nhân vật (tùy chọn)</label>
                        <input type="text" id="character_name" name="character_name" class="form-control" 
                               placeholder="Nhập tên nhân vật nếu có..." 
                               value="{{ old('character_name') }}">
                        <small style="opacity: 0.7; font-size: 12px;">Để trống nếu không liên quan đến nhân vật cụ thể</small>
                    </div>

                    <!-- Note -->
                    <div class="form-group">
                        <label for="note">Ghi chú</label>
                        <textarea id="note" name="note" class="form-control" rows="3" 
                                  placeholder="Nhập lý do nạp coin, ghi chú...">{{ old('note') }}</textarea>
                        <small style="opacity: 0.7; font-size: 12px;">Ghi chú sẽ được lưu trong lịch sử giao dịch</small>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="{{ route('admin.coin-recharge.index') }}" class="btn btn-secondary">
                        ❌ Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        ✅ Nạp coin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let searchTimeout;
        let selectedAccount = null;

        // Account search functionality
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value.trim();
            
            if (username.length < 2) {
                hideSearchResults();
                hideAccountInfo();
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchAccount(username);
            }, 300);
        });

        function searchAccount(username) {
            // In a real implementation, this would be an AJAX call
            // For now, we'll simulate the search
            console.log('Searching for:', username);
            hideSearchResults();
        }

        function hideSearchResults() {
            document.getElementById('searchResults').style.display = 'none';
        }

        function hideAccountInfo() {
            document.getElementById('accountInfo').style.display = 'none';
            selectedAccount = null;
        }

        // Amount calculation
        function setAmount(amount, coins) {
            document.getElementById('amount').value = amount;
            document.getElementById('coins_added').value = coins;
        }

        // Auto-calculate coins when amount changes
        document.getElementById('amount').addEventListener('input', function() {
            const amount = parseInt(this.value) || 0;
            document.getElementById('coins_added').value = amount; // 1:1 ratio
        });

        // Form validation
        document.getElementById('rechargeForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const amount = parseInt(document.getElementById('amount').value);
            const coins = parseInt(document.getElementById('coins_added').value);

            if (!username) {
                alert('Vui lòng nhập tên tài khoản');
                e.preventDefault();
                return;
            }

            if (amount < 1000 || amount > 100000000) {
                alert('Số tiền phải từ 1,000đ đến 100,000,000đ');
                e.preventDefault();
                return;
            }

            if (coins < 1 || coins > 1000000) {
                alert('Số coin phải từ 1 đến 1,000,000');
                e.preventDefault();
                return;
            }

            // Confirm before submit
            if (!confirm(`Bạn có chắc chắn muốn nạp ${coins.toLocaleString()} coin cho tài khoản "${username}"?`)) {
                e.preventDefault();
                return;
            }

            // Show loading state
            document.getElementById('submitBtn').textContent = '⏳ Đang xử lý...';
            document.getElementById('submitBtn').disabled = true;
        });

        // Format number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseInt(this.value);
                }
            });
        });
    </script>
@endsection

@section('scripts')
<script>
let searchTimeout;
        let selectedAccount = null;

        // Account search functionality
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value.trim();
            
            if (username.length < 2) {
                hideSearchResults();
                hideAccountInfo();
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchAccount(username);
            }, 300);
        });

        function searchAccount(username) {
            // In a real implementation, this would be an AJAX call
            // For now, we'll simulate the search
            console.log('Searching for:', username);
            hideSearchResults();
        }

        function hideSearchResults() {
            document.getElementById('searchResults').style.display = 'none';
        }

        function hideAccountInfo() {
            document.getElementById('accountInfo').style.display = 'none';
            selectedAccount = null;
        }

        // Amount calculation
        function setAmount(amount, coins) {
            document.getElementById('amount').value = amount;
            document.getElementById('coins_added').value = coins;
        }

        // Auto-calculate coins when amount changes
        document.getElementById('amount').addEventListener('input', function() {
            const amount = parseInt(this.value) || 0;
            document.getElementById('coins_added').value = amount; // 1:1 ratio
        });

        // Form validation
        document.getElementById('rechargeForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const amount = parseInt(document.getElementById('amount').value);
            const coins = parseInt(document.getElementById('coins_added').value);

            if (!username) {
                alert('Vui lòng nhập tên tài khoản');
                e.preventDefault();
                return;
            }

            if (amount < 1000 || amount > 100000000) {
                alert('Số tiền phải từ 1,000đ đến 100,000,000đ');
                e.preventDefault();
                return;
            }

            if (coins < 1 || coins > 1000000) {
                alert('Số coin phải từ 1 đến 1,000,000');
                e.preventDefault();
                return;
            }

            // Confirm before submit
            if (!confirm(`Bạn có chắc chắn muốn nạp ${coins.toLocaleString()} coin cho tài khoản "${username}"?`)) {
                e.preventDefault();
                return;
            }

            // Show loading state
            document.getElementById('submitBtn').textContent = '⏳ Đang xử lý...';
            document.getElementById('submitBtn').disabled = true;
        });

        // Format number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseInt(this.value);
                }
            });
        });
</script>
@endsection
