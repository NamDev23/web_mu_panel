@extends('layouts.user')

@section('title', 'Dịch vụ nhân vật - MU Game Portal')

@section('content')
<!-- Character Services Header -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-ninja"></i>
            Dịch vụ nhân vật
        </h3>
    </div>
    <div style="text-align: center;">
        @if(session('user_account.game_account_id'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.25rem; font-weight: 600; color: #166534; margin-bottom: 0.5rem;">
                    <i class="fas fa-link"></i> Tài khoản đã liên kết
                </div>
                <div style="color: #6b7280;">
                    Bạn có thể sử dụng các dịch vụ nhân vật bên dưới
                </div>
            </div>
        @else
            <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.25rem; font-weight: 600; color: #92400e; margin-bottom: 0.5rem;">
                    <i class="fas fa-unlink"></i> Chưa liên kết tài khoản game
                </div>
                <div style="color: #6b7280; margin-bottom: 1rem;">
                    Vui lòng liên kết tài khoản game để sử dụng dịch vụ nhân vật
                </div>
                <a href="{{ route('user.profile') }}" class="btn btn-warning">
                    <i class="fas fa-link"></i> Liên kết ngay
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Character Services -->
<div class="grid grid-2">
    <!-- Character Rename Service -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i>
                Đổi tên nhân vật
            </h3>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 500; color: #0369a1; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Thông tin dịch vụ
                </div>
                <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                    <li>Chi phí: 50,000 Coin</li>
                    <li>Thời gian xử lý: Tức thì</li>
                    <li>Tên mới phải từ 4-10 ký tự</li>
                    <li>Không được trùng với tên đã có</li>
                </ul>
            </div>
        </div>

        @if(session('user_account.game_account_id'))
            <form method="POST" action="{{ route('user.character.rename') }}" id="renameForm">
                @csrf

                <div class="form-group">
                    <label for="character_id" class="form-label">Chọn nhân vật</label>
                    <select name="character_id" id="character_id" class="form-select" required>
                        <option value="">Đang tải danh sách nhân vật...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_name" class="form-label">Tên mới</label>
                    <input
                        type="text"
                        name="new_name"
                        id="new_name"
                        class="form-input"
                        placeholder="Nhập tên mới cho nhân vật"
                        required
                        minlength="4"
                        maxlength="10"
                    >
                    <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                        4-10 ký tự, chỉ chữ cái và số
                    </div>
                </div>

                <button type="submit" class="btn btn-warning" disabled>
                    <i class="fas fa-coins"></i>
                    Đổi tên (50,000 Coin)
                </button>
            </form>
        @else
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-lock" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Cần liên kết tài khoản game</p>
            </div>
        @endif
    </div>

    <!-- Stats Reset Service -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-redo"></i>
                Reset điểm kỹ năng
            </h3>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1rem;">
                <div style="font-weight: 500; color: #0369a1; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Thông tin dịch vụ
                </div>
                <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                    <li>Chi phí: 30,000 Coin</li>
                    <li>Thời gian xử lý: Tức thì</li>
                    <li>Reset toàn bộ điểm Str, Agi, Vit, Ene</li>
                    <li>Không ảnh hưởng đến level</li>
                </ul>
            </div>
        </div>

        @if(session('user_account.game_account_id'))
            <form method="POST" action="{{ route('user.character.reset-stats') }}" id="resetStatsForm">
                @csrf

                <div class="form-group">
                    <label for="character_id_reset" class="form-label">Chọn nhân vật</label>
                    <select name="character_id" id="character_id_reset" class="form-select" required>
                        <option value="">Đang tải danh sách nhân vật...</option>
                    </select>
                </div>

                <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-weight: 500; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i> Cảnh báo
                    </div>
                    <div style="color: #92400e; font-size: 0.875rem;">
                        Hành động này sẽ reset toàn bộ điểm kỹ năng và không thể hoàn tác!
                    </div>
                </div>

                <button type="submit" class="btn btn-warning" disabled>
                    <i class="fas fa-coins"></i>
                    Reset Stats (30,000 Coin)
                </button>
            </form>
        @else
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-lock" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Cần liên kết tài khoản game</p>
            </div>
        @endif
    </div>
</div>

<!-- Character List (if linked) -->
@if(session('user_account.game_account_id'))
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            Danh sách nhân vật
        </h3>
    </div>
    <div id="characterList">
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>Đang tải danh sách nhân vật...</p>
        </div>
    </div>
</div>
@endif

<!-- Service History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Lịch sử dịch vụ
        </h3>
    </div>
    <div style="text-align: center; color: #6b7280; padding: 2rem;">
        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
        <p>Chưa có lịch sử sử dụng dịch vụ</p>
        <p style="font-size: 0.875rem; margin-top: 0.5rem;">
            Lịch sử sẽ hiển thị sau khi bạn sử dụng dịch vụ
        </p>
    </div>
</div>

<!-- Coming Soon Services -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-rocket"></i>
            Dịch vụ sắp ra mắt
        </h3>
    </div>
    <div class="grid grid-3">
        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
            <i class="fas fa-exchange-alt" style="font-size: 2rem; color: #6b7280; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #374151;">Đổi class</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Thay đổi class nhân vật</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
            <i class="fas fa-map-marker-alt" style="font-size: 2rem; color: #6b7280; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #374151;">Teleport</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Dịch chuyển nhân vật</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
            <i class="fas fa-shield-alt" style="font-size: 2rem; color: #6b7280; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #374151;">Unbug</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Sửa lỗi nhân vật</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(session('user_account.game_account_id'))
// Load character list
function loadCharacters() {
    // Mock data for now - in real implementation, this would be an AJAX call
    const characters = [
        { id: 1, name: 'WarriorKing', class: 'Dark Knight', level: 350 },
        { id: 2, name: 'MageQueen', class: 'Soul Master', level: 280 },
        { id: 3, name: 'ElfArcher', class: 'Muse Elf', level: 320 }
    ];

    const selectElements = ['character_id', 'character_id_reset'];

    selectElements.forEach(selectId => {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">Chọn nhân vật</option>';

        characters.forEach(char => {
            const option = document.createElement('option');
            option.value = char.id;
            option.textContent = `${char.name} (${char.class} - Level ${char.level})`;
            select.appendChild(option);
        });
    });

    // Update character list display
    const characterList = document.getElementById('characterList');
    if (characterList) {
        let html = '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">';

        characters.forEach(char => {
            html += `
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem;">
                    <div style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">${char.name}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">
                        <div>Class: ${char.class}</div>
                        <div>Level: ${char.level}</div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        characterList.innerHTML = html;
    }

    // Enable buttons when character is selected
    selectElements.forEach(selectId => {
        const select = document.getElementById(selectId);
        const form = select.closest('form');
        const button = form.querySelector('button[type="submit"]');

        select.addEventListener('change', function() {
            button.disabled = !this.value;
        });
    });
}

// Load characters when page loads
document.addEventListener('DOMContentLoaded', loadCharacters);

// Form submissions
document.getElementById('renameForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Chức năng đổi tên nhân vật đang được phát triển!');
});

document.getElementById('resetStatsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Bạn có chắc chắn muốn reset điểm kỹ năng? Hành động này không thể hoàn tác!')) {
        alert('Chức năng reset stats đang được phát triển!');
    }
});
@endif
@endsection
