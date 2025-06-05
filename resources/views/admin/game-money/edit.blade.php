@extends('layouts.admin')

@section('title', 'Chỉnh sửa xu game - {{ $account->UserName }} - MU Admin Panel')

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
    .form-container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 30px;
        color: white;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-weight: 600;
        margin-bottom: 8px;
        color: white;
    }
    .form-control {
        padding: 12px;
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
    }
    .btn-primary {
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        color: white;
    }
    .btn-secondary {
        background: rgba(107, 114, 128, 0.3);
        color: white;
        border: 1px solid rgba(107, 114, 128, 0.5);
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .alert-error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: white;
    }
    .help-text {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 5px;
    }
    .current-values {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .current-values h4 {
        margin-bottom: 10px;
        color: #10b981;
    }
    .value-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }
    .quick-btn {
        padding: 8px 12px;
        background: rgba(59, 130, 246, 0.2);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 6px;
        color: #3b82f6;
        cursor: pointer;
        text-align: center;
        font-size: 12px;
        transition: all 0.2s;
    }
    .quick-btn:hover {
        background: rgba(59, 130, 246, 0.3);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-actions {
            flex-direction: column;
        }
        .btn {
            width: 100%;
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">✏️ Chỉnh sửa xu game</h1>
        <p class="page-desc">Tài khoản: <strong>{{ $account->UserName }}</strong> (ID: {{ $account->ID }})</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                ❌ {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <!-- Form Container -->
    <div class="form-container">
        <!-- Current Values -->
        <div class="current-values">
            <h4>💰 Giá trị hiện tại</h4>
            <div class="value-row">
                <span>RealMoney:</span>
                <strong>{{ number_format($money->realmoney) }} RM</strong>
            </div>
            <div class="value-row">
                <span>Money (Zen):</span>
                <strong>{{ number_format($money->money) }} Zen</strong>
            </div>
            <div class="value-row">
                <span>Tổng tài sản:</span>
                <strong>{{ number_format($money->realmoney + $money->money) }}</strong>
            </div>
        </div>

        <form action="{{ route('admin.game-money.update', $account->ID) }}" method="POST" id="editForm">
            @csrf

            <div class="form-grid">
                <!-- Action Type -->
                <div class="form-group">
                    <label for="action_type">Loại thao tác *</label>
                    <select id="action_type" name="action_type" class="form-control" required>
                        <option value="set" {{ old('action_type') == 'set' ? 'selected' : '' }}>Đặt giá trị cụ thể</option>
                        <option value="add" {{ old('action_type') == 'add' ? 'selected' : '' }}>Cộng thêm</option>
                        <option value="subtract" {{ old('action_type') == 'subtract' ? 'selected' : '' }}>Trừ đi</option>
                    </select>
                    <div class="help-text">Chọn loại thao tác muốn thực hiện</div>
                </div>

                <!-- RealMoney -->
                <div class="form-group">
                    <label for="realmoney">RealMoney *</label>
                    <div class="quick-actions">
                        <div class="quick-btn" onclick="setRealMoney(1000)">+1K</div>
                        <div class="quick-btn" onclick="setRealMoney(5000)">+5K</div>
                        <div class="quick-btn" onclick="setRealMoney(10000)">+10K</div>
                        <div class="quick-btn" onclick="setRealMoney(50000)">+50K</div>
                        <div class="quick-btn" onclick="setRealMoney(100000)">+100K</div>
                        <div class="quick-btn" onclick="setRealMoney(0)">Reset</div>
                    </div>
                    <input type="number" id="realmoney" name="realmoney" class="form-control"
                           placeholder="0" value="{{ old('realmoney', 0) }}" min="0" max="2000000000" required>
                    <div class="help-text">Số RealMoney (xu thật) - tối đa 2,000,000,000</div>
                </div>

                <!-- Money -->
                <div class="form-group">
                    <label for="money">Money (Zen) *</label>
                    <div class="quick-actions">
                        <div class="quick-btn" onclick="setMoney(10000)">+10K</div>
                        <div class="quick-btn" onclick="setMoney(50000)">+50K</div>
                        <div class="quick-btn" onclick="setMoney(100000)">+100K</div>
                        <div class="quick-btn" onclick="setMoney(500000)">+500K</div>
                        <div class="quick-btn" onclick="setMoney(1000000)">+1M</div>
                        <div class="quick-btn" onclick="setMoney(0)">Reset</div>
                    </div>
                    <input type="number" id="money" name="money" class="form-control" 
                           placeholder="0" value="{{ old('money', 0) }}" min="0" max="2000000000" required>
                    <div class="help-text">Số Money (Zen) - tối đa 2,000,000,000</div>
                </div>

                <!-- Reason -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="reason">Lý do thay đổi *</label>
                    <textarea id="reason" name="reason" class="form-control" rows="3" 
                              placeholder="Nhập lý do thay đổi xu game..." required>{{ old('reason') }}</textarea>
                    <div class="help-text">Ghi rõ lý do để theo dõi và kiểm tra sau này</div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.game-money.show', $account->ID) }}" class="btn btn-secondary">❌ Hủy</a>
                <button type="submit" class="btn btn-primary">💰 Cập nhật xu</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick action functions
    window.setRealMoney = function(amount) {
        document.getElementById('realmoney').value = amount;
    };

    window.setMoney = function(amount) {
        document.getElementById('money').value = amount;
    };

    // Form validation
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const actionType = document.getElementById('action_type').value;
        const realmoney = parseInt(document.getElementById('realmoney').value) || 0;
        const money = parseInt(document.getElementById('money').value) || 0;
        const reason = document.getElementById('reason').value.trim();

        if (realmoney === 0 && money === 0) {
            alert('Vui lòng nhập ít nhất một trong hai: RealMoney hoặc Money');
            e.preventDefault();
            return;
        }

        if (!reason) {
            alert('Vui lòng nhập lý do thay đổi');
            e.preventDefault();
            return;
        }

        const actionText = {
            'set': 'đặt',
            'add': 'cộng thêm',
            'subtract': 'trừ đi'
        };

        if (!confirm(`Bạn có chắc chắn muốn ${actionText[actionType]}?\n\nRealMoney: ${realmoney.toLocaleString()}\nMoney: ${money.toLocaleString()}\nLý do: ${reason}`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
