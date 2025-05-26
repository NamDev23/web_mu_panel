@extends('layouts.admin')

@section('title', 'Tạo giftcode mới - MU Admin Panel')

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
        .giftcode-form {
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
        .form-section {
            margin-bottom: 30px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #3b82f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
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
        .form-control textarea {
            resize: vertical;
            min-height: 100px;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .radio-item input[type="radio"] {
            width: 18px;
            height: 18px;
        }
        .code-preview {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .reward-builder {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }
        .reward-type-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .reward-tab {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .reward-tab.active {
            background: rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
        }
        .reward-content {
            display: none;
        }
        .reward-content.active {
            display: block;
        }
        .item-builder {
            margin-top: 15px;
        }
        .item-row {
            display: grid;
            grid-template-columns: 1fr 100px 1fr auto;
            gap: 10px;
            align-items: end;
            margin-bottom: 10px;
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
            background: rgba(107, 114, 128, 0.8);
            color: white;
        }
        .btn-success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
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
        .help-text {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 5px;
        }
        .preview-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .preview-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #f59e0b;
        }
        .preview-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .preview-item:last-child {
            border-bottom: none;
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
            <a href="/admin/giftcodes">Quản lý giftcode</a> /
            Tạo giftcode mới
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

        <!-- Giftcode Form -->
        <div class="giftcode-form">
            <div class="form-header">
                <h1>🎁 Tạo giftcode mới</h1>
                <p>Tạo giftcode với phần thưởng tùy chỉnh cho người chơi</p>
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <h4>⚠️ Lưu ý quan trọng</h4>
                <p>Giftcode sau khi tạo sẽ không thể thay đổi code. Hãy kiểm tra kỹ thông tin trước khi tạo.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.giftcodes.store') }}" method="POST" id="giftcodeForm">
                @csrf

                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">📋 Thông tin cơ bản</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Tên giftcode *</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   placeholder="VD: Giftcode tân thủ, Event Tết..."
                                   value="{{ old('name') }}" required>
                            <div class="help-text">Tên mô tả cho giftcode này</div>
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea id="description" name="description" class="form-control"
                                      placeholder="Mô tả chi tiết về giftcode...">{{ old('description') }}</textarea>
                            <div class="help-text">Mô tả chi tiết về giftcode (tùy chọn)</div>
                        </div>
                    </div>
                </div>

                <!-- Code Configuration -->
                <div class="form-section">
                    <h3 class="section-title">🔑 Cấu hình mã code</h3>
                    <div class="form-group">
                        <label>Loại tạo code</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="single_code" name="code_type" value="single" checked>
                                <label for="single_code">Code đơn lẻ</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="multiple_code" name="code_type" value="multiple">
                                <label for="multiple_code">Tạo nhiều code</label>
                            </div>
                        </div>
                    </div>

                    <div id="single_code_section">
                        <div class="form-group">
                            <label for="code">Mã giftcode *</label>
                            <input type="text" id="code" name="code" class="form-control"
                                   placeholder="VD: WELCOME2024, NEWYEAR..."
                                   value="{{ old('code') }}" style="text-transform: uppercase;">
                            <div class="help-text">Mã giftcode duy nhất (chỉ chữ cái, số và dấu gạch dưới)</div>
                        </div>
                    </div>

                    <div id="multiple_code_section" style="display: none;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="code_prefix">Tiền tố code *</label>
                                <input type="text" id="code_prefix" name="code_prefix" class="form-control"
                                       placeholder="VD: EVENT, GIFT..."
                                       value="{{ old('code_prefix') }}" style="text-transform: uppercase;">
                                <div class="help-text">Tiền tố cho các code (sẽ thêm số thứ tự phía sau)</div>
                            </div>
                            <div class="form-group">
                                <label for="code_count">Số lượng code *</label>
                                <input type="number" id="code_count" name="code_count" class="form-control"
                                       placeholder="100" value="{{ old('code_count', 10) }}" min="1" max="1000">
                                <div class="help-text">Số lượng code cần tạo (tối đa 1000)</div>
                            </div>
                        </div>
                        <div class="code-preview" id="code_preview">
                            <strong>Ví dụ code sẽ tạo:</strong><br>
                            <span id="preview_codes">EVENT0001, EVENT0002, EVENT0003...</span>
                        </div>
                    </div>
                </div>

                <!-- Usage Configuration -->
                <div class="form-section">
                    <h3 class="section-title">⚙️ Cấu hình sử dụng</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="max_uses">Số lần sử dụng tối đa *</label>
                            <input type="number" id="max_uses" name="max_uses" class="form-control"
                                   placeholder="1" value="{{ old('max_uses', 1) }}" min="1" max="10000" required>
                            <div class="help-text">Số lần tối đa mỗi code có thể được sử dụng</div>
                        </div>

                        <div class="form-group">
                            <label for="expires_at">Ngày hết hạn</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" class="form-control"
                                   value="{{ old('expires_at') }}">
                            <div class="help-text">Để trống nếu không giới hạn thời gian</div>
                        </div>
                    </div>
                </div>

                <!-- Rewards Configuration -->
                <div class="form-section">
                    <h3 class="section-title">🎁 Cấu hình phần thưởng</h3>
                    <div class="form-group">
                        <label>Loại phần thưởng</label>
                        <div class="reward-type-tabs">
                            <div class="reward-tab active" data-type="coins">💰 Chỉ Coin</div>
                            <div class="reward-tab" data-type="items">🎁 Chỉ Item</div>
                            <div class="reward-tab" data-type="mixed">🎯 Coin + Item</div>
                        </div>
                        <input type="hidden" id="reward_type" name="reward_type" value="coins">
                    </div>

                    <!-- Coins Reward -->
                    <div class="reward-content active" id="coins_reward">
                        <div class="form-group">
                            <label for="reward_coins">Số coin thưởng *</label>
                            <input type="number" id="reward_coins" name="reward_coins" class="form-control"
                                   placeholder="10000" value="{{ old('reward_coins', 10000) }}" min="0">
                            <div class="help-text">Số coin người chơi sẽ nhận được</div>
                        </div>
                    </div>

                    <!-- Items Reward -->
                    <div class="reward-content" id="items_reward">
                        <div class="form-group">
                            <label>Danh sách item thưởng</label>
                            <textarea id="reward_items" name="reward_items" class="form-control" rows="6"
                                      placeholder="Nhập theo format: ID,Số lượng,Tên item (mỗi dòng 1 item)&#10;Ví dụ:&#10;1001,5,Kiếm thép&#10;2001,10,Bình máu lớn&#10;3001,1,Nhẫn sức mạnh">{{ old('reward_items') }}</textarea>
                            <div class="help-text">
                                Format: ID,Số lượng,Tên item (mỗi dòng 1 item)<br>
                                Ví dụ: 1001,5,Kiếm thép
                            </div>
                        </div>
                    </div>

                    <!-- Mixed Reward -->
                    <div class="reward-content" id="mixed_reward">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="reward_coins_mixed">Số coin thưởng *</label>
                                <input type="number" id="reward_coins_mixed" class="form-control"
                                       placeholder="5000" value="{{ old('reward_coins', 5000) }}" min="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Danh sách item thưởng</label>
                            <textarea id="reward_items_mixed" class="form-control" rows="4"
                                      placeholder="1001,3,Kiếm thép&#10;2001,5,Bình máu">{{ old('reward_items') }}</textarea>
                        </div>
                    </div>

                    <!-- Reward Preview -->
                    <div class="preview-section">
                        <div class="preview-title">👁️ Xem trước phần thưởng</div>
                        <div id="reward_preview">
                            <div class="preview-item">
                                <span>💰 Coin:</span>
                                <span id="preview_coins">10,000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="{{ route('admin.giftcodes.index') }}" class="btn btn-secondary">
                        ❌ Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        ✅ Tạo giftcode
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Code type switching
        document.querySelectorAll('input[name="code_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const singleSection = document.getElementById('single_code_section');
                const multipleSection = document.getElementById('multiple_code_section');

                if (this.value === 'single') {
                    singleSection.style.display = 'block';
                    multipleSection.style.display = 'none';
                    document.getElementById('code').required = true;
                    document.getElementById('code_prefix').required = false;
                    document.getElementById('code_count').required = false;
                } else {
                    singleSection.style.display = 'none';
                    multipleSection.style.display = 'block';
                    document.getElementById('code').required = false;
                    document.getElementById('code_prefix').required = true;
                    document.getElementById('code_count').required = true;
                    updateCodePreview();
                }
            });
        });

        // Code preview for multiple codes
        function updateCodePreview() {
            const prefix = document.getElementById('code_prefix').value.toUpperCase();
            const count = parseInt(document.getElementById('code_count').value) || 10;

            if (prefix) {
                const examples = [];
                for (let i = 1; i <= Math.min(count, 3); i++) {
                    examples.push(prefix + String(i).padStart(4, '0'));
                }
                if (count > 3) {
                    examples.push('...');
                }
                document.getElementById('preview_codes').textContent = examples.join(', ');
            }
        }

        document.getElementById('code_prefix').addEventListener('input', updateCodePreview);
        document.getElementById('code_count').addEventListener('input', updateCodePreview);

        // Reward type switching
        document.querySelectorAll('.reward-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.reward-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.reward-content').forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');
                const type = this.dataset.type;
                document.getElementById('reward_type').value = type;
                document.getElementById(type + '_reward').classList.add('active');

                // Update required fields
                updateRequiredFields(type);
                updateRewardPreview();
            });
        });

        function updateRequiredFields(type) {
            // Reset all required
            document.getElementById('reward_coins').required = false;
            document.getElementById('reward_items').required = false;

            if (type === 'coins') {
                document.getElementById('reward_coins').required = true;
            } else if (type === 'items') {
                document.getElementById('reward_items').required = true;
            } else if (type === 'mixed') {
                // For mixed, we'll handle validation in form submit
            }
        }

        // Reward preview
        function updateRewardPreview() {
            const type = document.getElementById('reward_type').value;
            const previewDiv = document.getElementById('reward_preview');

            let html = '';

            if (type === 'coins') {
                const coins = parseInt(document.getElementById('reward_coins').value) || 0;
                html = `<div class="preview-item"><span>💰 Coin:</span><span>${coins.toLocaleString()}</span></div>`;
            } else if (type === 'items') {
                const items = parseItems(document.getElementById('reward_items').value);
                html = '<div class="preview-item"><span>🎁 Items:</span><span>' + items.length + ' loại</span></div>';
                items.forEach(item => {
                    html += `<div class="preview-item"><span>  • ${item.name}:</span><span>${item.quantity}</span></div>`;
                });
            } else if (type === 'mixed') {
                const coins = parseInt(document.getElementById('reward_coins_mixed').value) || 0;
                const items = parseItems(document.getElementById('reward_items_mixed').value);
                html = `<div class="preview-item"><span>💰 Coin:</span><span>${coins.toLocaleString()}</span></div>`;
                html += '<div class="preview-item"><span>🎁 Items:</span><span>' + items.length + ' loại</span></div>';
                items.forEach(item => {
                    html += `<div class="preview-item"><span>  • ${item.name}:</span><span>${item.quantity}</span></div>`;
                });
            }

            previewDiv.innerHTML = html;
        }

        function parseItems(itemsText) {
            const items = [];
            const lines = itemsText.split('\n');
            lines.forEach(line => {
                line = line.trim();
                if (line) {
                    const parts = line.split(',');
                    if (parts.length >= 2) {
                        items.push({
                            id: parts[0].trim(),
                            quantity: parseInt(parts[1].trim()) || 1,
                            name: parts[2] ? parts[2].trim() : 'Item'
                        });
                    }
                }
            });
            return items;
        }

        // Update preview when inputs change
        document.getElementById('reward_coins').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_items').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_coins_mixed').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_items_mixed').addEventListener('input', updateRewardPreview);

        // Auto uppercase for codes
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        document.getElementById('code_prefix').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Form validation
        document.getElementById('giftcodeForm').addEventListener('submit', function(e) {
            const codeType = document.querySelector('input[name="code_type"]:checked').value;
            const rewardType = document.getElementById('reward_type').value;

            // Validate code fields
            if (codeType === 'single') {
                const code = document.getElementById('code').value.trim();
                if (!code) {
                    alert('Vui lòng nhập mã giftcode');
                    e.preventDefault();
                    return;
                }
            } else if (codeType === 'multiple') {
                const prefix = document.getElementById('code_prefix').value.trim();
                const count = document.getElementById('code_count').value;

                if (!prefix) {
                    alert('Vui lòng nhập tiền tố code');
                    e.preventDefault();
                    return;
                }

                if (!count || count < 1 || count > 1000) {
                    alert('Số lượng code phải từ 1 đến 1000');
                    e.preventDefault();
                    return;
                }

                // Validate prefix format
                if (!/^[A-Z0-9_]+$/.test(prefix)) {
                    alert('Tiền tố code chỉ được chứa chữ cái, số và dấu gạch dưới');
                    e.preventDefault();
                    return;
                }
            }

            // Validate rewards
            if (rewardType === 'coins') {
                const coins = document.getElementById('reward_coins').value;
                if (!coins || coins < 0) {
                    alert('Vui lòng nhập số coin thưởng hợp lệ');
                    e.preventDefault();
                    return;
                }
            } else if (rewardType === 'items') {
                const items = document.getElementById('reward_items').value.trim();
                if (!items) {
                    alert('Vui lòng nhập danh sách item thưởng');
                    e.preventDefault();
                    return;
                }
            } else if (rewardType === 'mixed') {
                const coins = document.getElementById('reward_coins_mixed').value;
                const items = document.getElementById('reward_items_mixed').value.trim();

                if (!coins || coins < 0) {
                    alert('Vui lòng nhập số coin thưởng hợp lệ');
                    e.preventDefault();
                    return;
                }

                if (!items) {
                    alert('Vui lòng nhập danh sách item thưởng');
                    e.preventDefault();
                    return;
                }

                // Copy mixed values to main fields
                document.getElementById('reward_coins').value = coins;
                document.getElementById('reward_items').value = items;
            }

            // Show loading state
            document.getElementById('submitBtn').textContent = '⏳ Đang tạo...';
            document.getElementById('submitBtn').disabled = true;
        });

        // Initialize
        updateRewardPreview();
    </script>
@endsection

@section('scripts')
<script>
// Code type switching
        document.querySelectorAll('input[name="code_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const singleSection = document.getElementById('single_code_section');
                const multipleSection = document.getElementById('multiple_code_section');

                if (this.value === 'single') {
                    singleSection.style.display = 'block';
                    multipleSection.style.display = 'none';
                    document.getElementById('code').required = true;
                    document.getElementById('code_prefix').required = false;
                    document.getElementById('code_count').required = false;
                } else {
                    singleSection.style.display = 'none';
                    multipleSection.style.display = 'block';
                    document.getElementById('code').required = false;
                    document.getElementById('code_prefix').required = true;
                    document.getElementById('code_count').required = true;
                    updateCodePreview();
                }
            });
        });

        // Code preview for multiple codes
        function updateCodePreview() {
            const prefix = document.getElementById('code_prefix').value.toUpperCase();
            const count = parseInt(document.getElementById('code_count').value) || 10;

            if (prefix) {
                const examples = [];
                for (let i = 1; i <= Math.min(count, 3); i++) {
                    examples.push(prefix + String(i).padStart(4, '0'));
                }
                if (count > 3) {
                    examples.push('...');
                }
                document.getElementById('preview_codes').textContent = examples.join(', ');
            }
        }

        document.getElementById('code_prefix').addEventListener('input', updateCodePreview);
        document.getElementById('code_count').addEventListener('input', updateCodePreview);

        // Reward type switching
        document.querySelectorAll('.reward-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.reward-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.reward-content').forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');
                const type = this.dataset.type;
                document.getElementById('reward_type').value = type;
                document.getElementById(type + '_reward').classList.add('active');

                // Update required fields
                updateRequiredFields(type);
                updateRewardPreview();
            });
        });

        function updateRequiredFields(type) {
            // Reset all required
            document.getElementById('reward_coins').required = false;
            document.getElementById('reward_items').required = false;

            if (type === 'coins') {
                document.getElementById('reward_coins').required = true;
            } else if (type === 'items') {
                document.getElementById('reward_items').required = true;
            } else if (type === 'mixed') {
                // For mixed, we'll handle validation in form submit
            }
        }

        // Reward preview
        function updateRewardPreview() {
            const type = document.getElementById('reward_type').value;
            const previewDiv = document.getElementById('reward_preview');

            let html = '';

            if (type === 'coins') {
                const coins = parseInt(document.getElementById('reward_coins').value) || 0;
                html = `<div class="preview-item"><span>💰 Coin:</span><span>${coins.toLocaleString()}</span></div>`;
            } else if (type === 'items') {
                const items = parseItems(document.getElementById('reward_items').value);
                html = '<div class="preview-item"><span>🎁 Items:</span><span>' + items.length + ' loại</span></div>';
                items.forEach(item => {
                    html += `<div class="preview-item"><span>  • ${item.name}:</span><span>${item.quantity}</span></div>`;
                });
            } else if (type === 'mixed') {
                const coins = parseInt(document.getElementById('reward_coins_mixed').value) || 0;
                const items = parseItems(document.getElementById('reward_items_mixed').value);
                html = `<div class="preview-item"><span>💰 Coin:</span><span>${coins.toLocaleString()}</span></div>`;
                html += '<div class="preview-item"><span>🎁 Items:</span><span>' + items.length + ' loại</span></div>';
                items.forEach(item => {
                    html += `<div class="preview-item"><span>  • ${item.name}:</span><span>${item.quantity}</span></div>`;
                });
            }

            previewDiv.innerHTML = html;
        }

        function parseItems(itemsText) {
            const items = [];
            const lines = itemsText.split('\n');
            lines.forEach(line => {
                line = line.trim();
                if (line) {
                    const parts = line.split(',');
                    if (parts.length >= 2) {
                        items.push({
                            id: parts[0].trim(),
                            quantity: parseInt(parts[1].trim()) || 1,
                            name: parts[2] ? parts[2].trim() : 'Item'
                        });
                    }
                }
            });
            return items;
        }

        // Update preview when inputs change
        document.getElementById('reward_coins').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_items').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_coins_mixed').addEventListener('input', updateRewardPreview);
        document.getElementById('reward_items_mixed').addEventListener('input', updateRewardPreview);

        // Auto uppercase for codes
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        document.getElementById('code_prefix').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Form validation
        document.getElementById('giftcodeForm').addEventListener('submit', function(e) {
            const codeType = document.querySelector('input[name="code_type"]:checked').value;
            const rewardType = document.getElementById('reward_type').value;

            // Validate code fields
            if (codeType === 'single') {
                const code = document.getElementById('code').value.trim();
                if (!code) {
                    alert('Vui lòng nhập mã giftcode');
                    e.preventDefault();
                    return;
                }
            } else if (codeType === 'multiple') {
                const prefix = document.getElementById('code_prefix').value.trim();
                const count = document.getElementById('code_count').value;

                if (!prefix) {
                    alert('Vui lòng nhập tiền tố code');
                    e.preventDefault();
                    return;
                }

                if (!count || count < 1 || count > 1000) {
                    alert('Số lượng code phải từ 1 đến 1000');
                    e.preventDefault();
                    return;
                }

                // Validate prefix format
                if (!/^[A-Z0-9_]+$/.test(prefix)) {
                    alert('Tiền tố code chỉ được chứa chữ cái, số và dấu gạch dưới');
                    e.preventDefault();
                    return;
                }
            }

            // Validate rewards
            if (rewardType === 'coins') {
                const coins = document.getElementById('reward_coins').value;
                if (!coins || coins < 0) {
                    alert('Vui lòng nhập số coin thưởng hợp lệ');
                    e.preventDefault();
                    return;
                }
            } else if (rewardType === 'items') {
                const items = document.getElementById('reward_items').value.trim();
                if (!items) {
                    alert('Vui lòng nhập danh sách item thưởng');
                    e.preventDefault();
                    return;
                }
            } else if (rewardType === 'mixed') {
                const coins = document.getElementById('reward_coins_mixed').value;
                const items = document.getElementById('reward_items_mixed').value.trim();

                if (!coins || coins < 0) {
                    alert('Vui lòng nhập số coin thưởng hợp lệ');
                    e.preventDefault();
                    return;
                }

                if (!items) {
                    alert('Vui lòng nhập danh sách item thưởng');
                    e.preventDefault();
                    return;
                }

                // Copy mixed values to main fields
                document.getElementById('reward_coins').value = coins;
                document.getElementById('reward_items').value = items;
            }

            // Show loading state
            document.getElementById('submitBtn').textContent = '⏳ Đang tạo...';
            document.getElementById('submitBtn').disabled = true;
        });

        // Initialize
        updateRewardPreview();
</script>
@endsection
