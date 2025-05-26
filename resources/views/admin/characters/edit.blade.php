@extends('layouts.admin')

@section('title', 'Chỉnh sửa nhân vật: {{ $character->rname }} - MU Admin Panel')

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
        .content-card {
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        .form-control[readonly] {
            background: rgba(255, 255, 255, 0.05);
            opacity: 0.7;
            cursor: not-allowed;
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
        .character-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .character-info h3 {
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
        .occupation-options {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 5px;
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
            <a href="/admin/characters">Quản lý nhân vật</a> /
            <a href="{{ route('admin.characters.show', $character->rid) }}">{{ $character->rname }}</a> /
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
                <h1>✏️ Chỉnh sửa nhân vật</h1>
                <p>Cập nhật thông tin và thống kê của nhân vật {{ $character->rname }}</p>
            </div>

            <!-- Character Info -->
            <div class="character-info">
                <h3>📋 Thông tin hiện tại</h3>
                <div class="info-row">
                    <span class="info-label">ID nhân vật:</span>
                    <span class="info-value">{{ $character->rid }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên nhân vật:</span>
                    <span class="info-value">{{ $character->rname }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tài khoản:</span>
                    <span class="info-value">{{ $character->username ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Server:</span>
                    <span class="info-value">Server {{ $character->serverid }}</span>
                </div>
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <h4>⚠️ Lưu ý quan trọng</h4>
                <p>Việc thay đổi thông tin nhân vật có thể ảnh hưởng đến trải nghiệm game của người chơi. Hãy cân nhắc kỹ trước khi thực hiện thay đổi.</p>
            </div>

            <!-- Edit Form -->
            <form action="{{ route('admin.characters.update', $character->rid) }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="level">Level *</label>
                        <input type="number" id="level" name="level" class="form-control" 
                               value="{{ old('level', $character->level) }}" 
                               min="1" max="400" required>
                        <small style="opacity: 0.7; font-size: 12px;">Level từ 1 đến 400</small>
                    </div>

                    <div class="form-group">
                        <label for="experience">Kinh nghiệm *</label>
                        <input type="number" id="experience" name="experience" class="form-control" 
                               value="{{ old('experience', $character->experience) }}" 
                               min="0" required>
                        <small style="opacity: 0.7; font-size: 12px;">Điểm kinh nghiệm hiện tại</small>
                    </div>

                    <div class="form-group">
                        <label for="money">Tiền trong game *</label>
                        <input type="number" id="money" name="money" class="form-control" 
                               value="{{ old('money', $character->money) }}" 
                               min="0" required>
                        <small style="opacity: 0.7; font-size: 12px;">Số tiền hiện có trong game</small>
                    </div>

                    <div class="form-group">
                        <label for="occupation">Nghề nghiệp *</label>
                        <select id="occupation" name="occupation" class="form-control" required>
                            <option value="0" {{ old('occupation', $character->occupation) == 0 ? 'selected' : '' }}>Chiến binh (0)</option>
                            <option value="1" {{ old('occupation', $character->occupation) == 1 ? 'selected' : '' }}>Pháp sư (1)</option>
                            <option value="2" {{ old('occupation', $character->occupation) == 2 ? 'selected' : '' }}>Cung thủ (2)</option>
                            <option value="3" {{ old('occupation', $character->occupation) == 3 ? 'selected' : '' }}>Đấu sĩ (3)</option>
                            <option value="4" {{ old('occupation', $character->occupation) == 4 ? 'selected' : '' }}>Thần quan (4)</option>
                            <option value="5" {{ old('occupation', $character->occupation) == 5 ? 'selected' : '' }}>Ám sát (5)</option>
                        </select>
                        <div class="occupation-options">
                            💡 Chọn nghề nghiệp phù hợp với nhân vật
                        </div>
                    </div>
                </div>

                <!-- Read-only fields -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên nhân vật</label>
                        <input type="text" class="form-control" value="{{ $character->rname }}" readonly>
                        <small style="opacity: 0.7; font-size: 12px;">Không thể thay đổi tên nhân vật</small>
                    </div>

                    <div class="form-group">
                        <label>Server</label>
                        <input type="text" class="form-control" value="Server {{ $character->serverid }}" readonly>
                        <small style="opacity: 0.7; font-size: 12px;">Không thể thay đổi server</small>
                    </div>

                    <div class="form-group">
                        <label>Ngày tạo</label>
                        <input type="text" class="form-control" 
                               value="{{ $character->regtime ? date('d/m/Y H:i', strtotime($character->regtime)) : 'N/A' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Lần online cuối</label>
                        <input type="text" class="form-control" 
                               value="{{ $character->lasttime ? date('d/m/Y H:i', strtotime($character->lasttime)) : 'Chưa online' }}" readonly>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="{{ route('admin.characters.show', $character->rid) }}" class="btn btn-secondary">
                        ❌ Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary">
                        ✅ Cập nhật nhân vật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-format number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.name === 'money' || this.name === 'experience') {
                    // Format large numbers with commas for better readability
                    let value = this.value.replace(/,/g, '');
                    if (!isNaN(value) && value !== '') {
                        this.setAttribute('data-raw-value', value);
                    }
                }
            });
        });

        // Validate form before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const level = parseInt(document.getElementById('level').value);
            const experience = parseInt(document.getElementById('experience').value);
            const money = parseInt(document.getElementById('money').value);

            if (level < 1 || level > 400) {
                alert('Level phải từ 1 đến 400');
                e.preventDefault();
                return;
            }

            if (experience < 0) {
                alert('Kinh nghiệm không thể âm');
                e.preventDefault();
                return;
            }

            if (money < 0) {
                alert('Tiền không thể âm');
                e.preventDefault();
                return;
            }

            // Confirm before submit
            if (!confirm('Bạn có chắc chắn muốn cập nhật thông tin nhân vật này?')) {
                e.preventDefault();
            }
        });
    </script>
@endsection

@section('scripts')
<script>
// Auto-format number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.name === 'money' || this.name === 'experience') {
                    // Format large numbers with commas for better readability
                    let value = this.value.replace(/,/g, '');
                    if (!isNaN(value) && value !== '') {
                        this.setAttribute('data-raw-value', value);
                    }
                }
            });
        });

        // Validate form before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const level = parseInt(document.getElementById('level').value);
            const experience = parseInt(document.getElementById('experience').value);
            const money = parseInt(document.getElementById('money').value);

            if (level < 1 || level > 400) {
                alert('Level phải từ 1 đến 400');
                e.preventDefault();
                return;
            }

            if (experience < 0) {
                alert('Kinh nghiệm không thể âm');
                e.preventDefault();
                return;
            }

            if (money < 0) {
                alert('Tiền không thể âm');
                e.preventDefault();
                return;
            }

            // Confirm before submit
            if (!confirm('Bạn có chắc chắn muốn cập nhật thông tin nhân vật này?')) {
                e.preventDefault();
            }
        });
</script>
@endsection
