@extends('layouts.admin')

@section('title', 'Chỉnh sửa giftcode: ' . $giftcode->code . ' - MU Admin Panel')

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
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .giftcode-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .giftcode-info h3 {
            margin-bottom: 15px;
            color: #3b82f6;
        }
        .giftcode-code {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #3b82f6;
            display: inline-block;
            margin-bottom: 10px;
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
        .usage-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .usage-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #8b5cf6;
        }
        .usage-progress {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: 10px 0;
        }
        .usage-bar {
            height: 100%;
            background: linear-gradient(45deg, #10b981, #059669);
            transition: width 0.3s;
        }
        .usage-text {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
        }
        .rewards-display {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }
        .rewards-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #f59e0b;
        }
        .reward-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .reward-item:last-child {
            border-bottom: none;
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
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            color: white;
            min-width: 400px;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
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
</style>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a> /
        <a href="/admin/giftcodes">Quản lý giftcode</a> /
        <a href="{{ route('admin.giftcodes.show', $giftcode->id) }}">{{ $giftcode->code }}</a> /
        Chỉnh sửa
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

    <!-- Edit Form -->
    <div class="content-card">
            <div class="form-header">
                <h1>✏️ Chỉnh sửa giftcode</h1>
                <p>Cập nhật thông tin giftcode {{ $giftcode->code }}</p>
            </div>

            <!-- Giftcode Info -->
            <div class="giftcode-info">
                <h3>📋 Thông tin giftcode</h3>
                <div class="giftcode-code">{{ $giftcode->code }}</div>
                <div class="info-row">
                    <span class="info-label">Ngày tạo:</span>
                    <span class="info-value">{{ date('d/m/Y H:i', strtotime($giftcode->created_at)) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tạo bởi:</span>
                    <span class="info-value">{{ $giftcode->admin_username ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="usage-info">
                <div class="usage-title">📊 Thống kê sử dụng</div>
                @php
                    $usagePercent = $giftcode->max_uses > 0 ? ($giftcode->used_count / $giftcode->max_uses) * 100 : 0;
                @endphp
                <div class="info-row">
                    <span class="info-label">Đã sử dụng:</span>
                    <span class="info-value">{{ $giftcode->used_count }} / {{ $giftcode->max_uses }} lượt</span>
                </div>
                <div class="usage-progress">
                    <div class="usage-bar" style="width: {{ $usagePercent }}%"></div>
                </div>
                <div class="usage-text">{{ number_format($usagePercent, 1) }}% đã sử dụng</div>
            </div>

            <!-- Rewards Display -->
            <div class="rewards-display">
                <div class="rewards-title">🎁 Phần thưởng hiện tại</div>
                @if(isset($giftcode->rewards['coins']) && $giftcode->rewards['coins'] > 0)
                    <div class="reward-item">
                        <span>💰 Coin:</span>
                        <span>{{ number_format($giftcode->rewards['coins']) }}</span>
                    </div>
                @endif

                @if(isset($giftcode->rewards['items']) && count($giftcode->rewards['items']) > 0)
                    @foreach($giftcode->rewards['items'] as $item)
                        <div class="reward-item">
                            <span>🎁 {{ $item['name'] }}:</span>
                            <span>{{ $item['quantity'] }}</span>
                        </div>
                    @endforeach
                @endif

                @if((!isset($giftcode->rewards['coins']) || $giftcode->rewards['coins'] == 0) &&
                    (!isset($giftcode->rewards['items']) || count($giftcode->rewards['items']) == 0))
                    <div style="text-align: center; opacity: 0.7; padding: 20px;">
                        Không có phần thưởng
                    </div>
                @endif
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <h4>⚠️ Lưu ý quan trọng</h4>
                <p>Chỉ có thể chỉnh sửa một số thông tin cơ bản. Mã giftcode và phần thưởng không thể thay đổi sau khi tạo.</p>
            </div>

            <!-- Edit Form -->
            <form action="{{ route('admin.giftcodes.update', $giftcode->id) }}" method="POST" id="editForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Tên giftcode *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="{{ old('name', $giftcode->name) }}" required>
                        <div class="help-text">Tên mô tả cho giftcode</div>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $giftcode->description) }}</textarea>
                        <div class="help-text">Mô tả chi tiết về giftcode</div>
                    </div>

                    <div class="form-group">
                        <label for="max_uses">Số lần sử dụng tối đa *</label>
                        <input type="number" id="max_uses" name="max_uses" class="form-control"
                               value="{{ old('max_uses', $giftcode->max_uses) }}"
                               min="{{ $giftcode->used_count }}" max="10000" required>
                        <div class="help-text">Tối thiểu {{ $giftcode->used_count }} (đã sử dụng)</div>
                    </div>

                    <div class="form-group">
                        <label for="expires_at">Ngày hết hạn</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" class="form-control"
                               value="{{ old('expires_at', $giftcode->expires_at ? date('Y-m-d\TH:i', strtotime($giftcode->expires_at)) : '') }}">
                        <div class="help-text">Để trống nếu không giới hạn thời gian</div>
                    </div>
                </div>

                <!-- Read-only fields -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã giftcode</label>
                        <input type="text" class="form-control" value="{{ $giftcode->code }}" readonly>
                        <div class="help-text">Không thể thay đổi mã giftcode</div>
                    </div>

                    <div class="form-group">
                        <label>Đã sử dụng</label>
                        <input type="text" class="form-control" value="{{ $giftcode->used_count }} lượt" readonly>
                        <div class="help-text">Số lần đã được sử dụng</div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" {{ $giftcode->is_active ? 'checked' : '' }}>
                        <label for="is_active">Kích hoạt giftcode</label>
                    </div>
                    <div class="help-text">Bỏ tick để vô hiệu hóa giftcode</div>
                </div>

                <div class="form-buttons">
                    <a href="{{ route('admin.giftcodes.show', $giftcode->id) }}" class="btn btn-secondary">
                        ❌ Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        ✅ Cập nhật giftcode
                    </button>
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal()">
                        🗑️ Xóa giftcode
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 20px;">🗑️ Xóa giftcode</h3>
            <p style="margin-bottom: 20px;">
                Bạn có chắc chắn muốn xóa giftcode <strong>{{ $giftcode->code }}</strong>?<br>
                Hành động này không thể hoàn tác.
            </p>
            <form action="{{ route('admin.giftcodes.destroy', $giftcode->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa giftcode</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function showDeleteModal() {
        document.getElementById('deleteModal').style.display = 'block';
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Form validation
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const maxUses = parseInt(document.getElementById('max_uses').value);
        const usedCount = {{ $giftcode->used_count }};

        if (maxUses < usedCount) {
            alert(`Số lần sử dụng tối đa không thể nhỏ hơn ${usedCount} (đã sử dụng)`);
            e.preventDefault();
            return;
        }

        // Show loading state
        document.getElementById('submitBtn').textContent = '⏳ Đang cập nhật...';
        document.getElementById('submitBtn').disabled = true;
    });
</script>
@endsection
