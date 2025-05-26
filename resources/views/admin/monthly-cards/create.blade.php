@extends('layouts.admin')

@section('title', 'Tạo Monthly Card - MU Admin Panel')

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
            text-align: center;
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
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .alert alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .help-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
        }
        .account-search {
            position: relative;
        }
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .search-result-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .search-result-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .result-username {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .result-email {
            font-size: 12px;
            opacity: 0.7;
        }
</style>
@endsection

@section('content')
</div>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">🎫 Tạo Monthly Card</h1>
            <p class="page-subtitle">Tạo thẻ tháng mới với phần thưởng hàng ngày cho người chơi</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-error">
                <strong>❌ Có lỗi xảy ra:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <div class="form-container">
            <form action="{{ route('admin.monthly-cards.store') }}" method="POST" id="createForm">
                @csrf

                <!-- Account Search -->
                <div class="form-group">
                    <label for="username">Tài khoản người chơi *</label>
                    <div class="account-search">
                        <input type="text" id="usernameSearch" class="form-control" placeholder="Tìm kiếm username, email hoặc tên..." autocomplete="off">
                        <input type="hidden" name="username" id="selectedUsername" value="{{ old('username') }}">
                        <div class="search-results" id="searchResults"></div>
                    </div>
                    <div class="help-text">Nhập ít nhất 2 ký tự để tìm kiếm tài khoản</div>
                </div>

                <!-- Package Details -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="package_name">Tên gói thẻ tháng *</label>
                        <input type="text" name="package_name" id="package_name" class="form-control" placeholder="VD: Premium Monthly Card" value="{{ old('package_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Giá (VNĐ) *</label>
                        <input type="number" name="price" id="price" class="form-control" placeholder="99000" value="{{ old('price') }}" min="0" step="1000" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="duration_days">Thời hạn (ngày) *</label>
                    <input type="number" name="duration_days" id="duration_days" class="form-control" placeholder="30" value="{{ old('duration_days', 30) }}" min="1" max="365" required>
                    <div class="help-text">Số ngày thẻ có hiệu lực (1-365 ngày)</div>
                </div>

                <!-- Rewards -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="daily_coins">Coins hàng ngày *</label>
                        <input type="number" name="daily_coins" id="daily_coins" class="form-control" placeholder="1000" value="{{ old('daily_coins') }}" min="1" required>
                        <div class="help-text">Số coins nhận được mỗi ngày</div>
                    </div>
                    <div class="form-group">
                        <label for="bonus_coins">Bonus coins (tùy chọn)</label>
                        <input type="number" name="bonus_coins" id="bonus_coins" class="form-control" placeholder="5000" value="{{ old('bonus_coins') }}" min="0">
                        <div class="help-text">Coins thưởng khi mua thẻ</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="daily_items">Items hàng ngày (tùy chọn)</label>
                    <input type="text" name="daily_items" id="daily_items" class="form-control" placeholder="VD: Potion x5, Gem x1" value="{{ old('daily_items') }}">
                    <div class="help-text">Mô tả items nhận được mỗi ngày</div>
                </div>

                <div class="form-group">
                    <label for="bonus_items">Bonus items (tùy chọn)</label>
                    <input type="text" name="bonus_items" id="bonus_items" class="form-control" placeholder="VD: VIP Weapon x1" value="{{ old('bonus_items') }}">
                    <div class="help-text">Items thưởng khi mua thẻ</div>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả (tùy chọn)</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Mô tả chi tiết về thẻ tháng...">{{ old('description') }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="btn-group">
                    <a href="{{ route('admin.monthly-cards.index') }}" class="btn btn-secondary">❌ Hủy</a>
                    <button type="submit" class="btn btn-primary">✅ Tạo thẻ tháng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Account search functionality
        const usernameSearch = document.getElementById('usernameSearch');
        const selectedUsername = document.getElementById('selectedUsername');
        const searchResults = document.getElementById('searchResults');

        usernameSearch.addEventListener('input', function() {
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            fetch(`/admin/monthly-cards/search-account?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(accounts => {
                    if (accounts.length > 0) {
                        searchResults.innerHTML = accounts.map(account => `
                            <div class="search-result-item" onclick="selectAccount('${account.username}', '${account.email || ''}')">
                                <div class="result-username">${account.username}</div>
                                <div class="result-email">${account.email || ''} ${account.full_name ? '- ' + account.full_name : ''}</div>
                            </div>
                        `).join('');
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="search-result-item">Không tìm thấy tài khoản</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.style.display = 'none';
                });
        });

        function selectAccount(username, email) {
            usernameSearch.value = `${username} (${email})`;
            selectedUsername.value = username;
            searchResults.style.display = 'none';
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.account-search')) {
                searchResults.style.display = 'none';
            }
        });
    </script>
@endsection

@section('scripts')
<script>
// Account search functionality
        const usernameSearch = document.getElementById('usernameSearch');
        const selectedUsername = document.getElementById('selectedUsername');
        const searchResults = document.getElementById('searchResults');

        usernameSearch.addEventListener('input', function() {
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            fetch(`/admin/monthly-cards/search-account?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(accounts => {
                    if (accounts.length > 0) {
                        searchResults.innerHTML = accounts.map(account => `
                            <div class="search-result-item" onclick="selectAccount('${account.username}', '${account.email || ''}')">
                                <div class="result-username">${account.username}</div>
                                <div class="result-email">${account.email || ''} ${account.full_name ? '- ' + account.full_name : ''}</div>
                            </div>
                        `).join('');
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="search-result-item">Không tìm thấy tài khoản</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.style.display = 'none';
                });
        });

        function selectAccount(username, email) {
            usernameSearch.value = `${username} (${email})`;
            selectedUsername.value = username;
            searchResults.style.display = 'none';
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.account-search')) {
                searchResults.style.display = 'none';
            }
        });
</script>
@endsection
