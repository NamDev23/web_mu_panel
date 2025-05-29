@extends('layouts.user')

@section('title', 'Nhập Giftcode - MU Game Portal')

@section('content')
<!-- Giftcode Input -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-gift"></i>
            Nhập Giftcode
        </h3>
    </div>
    <div style="margin-bottom: 1.5rem;">
        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 500; color: #0369a1; margin-bottom: 0.5rem;">
                <i class="fas fa-info-circle"></i> Hướng dẫn sử dụng Giftcode
            </div>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Nhập chính xác mã giftcode (phân biệt chữ hoa/thường)</li>
                <li>Mỗi giftcode chỉ có thể sử dụng 1 lần</li>
                <li>Kiểm tra hạn sử dụng trước khi nhập</li>
                <li>Phần thưởng sẽ được gửi vào tài khoản ngay lập tức</li>
            </ul>
        </div>
    </div>

    @if(session('user_account.game_account_id'))
        <form method="POST" action="{{ route('user.giftcode.redeem') }}" id="giftcodeForm">
            @csrf

            <div class="form-group">
                <label for="giftcode" class="form-label">Mã Giftcode</label>
                <div style="display: flex; gap: 1rem;">
                    <input
                        type="text"
                        name="giftcode"
                        id="giftcode"
                        class="form-input"
                        placeholder="Nhập mã giftcode"
                        required
                        style="flex: 1; text-transform: uppercase;"
                        maxlength="20"
                    >
                    <button type="submit" class="btn btn-primary" style="min-width: 120px;">
                        <i class="fas fa-gift"></i>
                        Nhập code
                    </button>
                </div>
                <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">
                    Mã giftcode thường có 8-20 ký tự
                </div>
            </div>
        </form>
    @else
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-unlink" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>Cần liên kết tài khoản game để sử dụng giftcode</p>
            <a href="{{ route('user.profile') }}" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-link"></i> Liên kết ngay
            </a>
        </div>
    @endif
</div>

<!-- Active Giftcodes -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags"></i>
            Giftcode đang hoạt động
        </h3>
    </div>
    <div id="activeGiftcodes">
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>Đang tải danh sách giftcode...</p>
        </div>
    </div>
</div>

<!-- Giftcode History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Lịch sử sử dụng
        </h3>
    </div>
    @if(session('user_account.game_account_id'))
        <div id="giftcodeHistory">
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Đang tải lịch sử...</p>
            </div>
        </div>
    @else
        <div style="text-align: center; color: #6b7280; padding: 2rem;">
            <i class="fas fa-lock" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>Cần liên kết tài khoản game để xem lịch sử</p>
        </div>
    @endif
</div>

<!-- Giftcode Types Info -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-question-circle"></i>
            Loại Giftcode
        </h3>
    </div>
    <div class="grid grid-3">
        <div style="text-align: center; padding: 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
            <i class="fas fa-calendar-day" style="font-size: 2rem; color: #10b981; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #166534;">Event Code</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Giftcode từ các sự kiện đặc biệt</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #fef3c7; border: 1px solid #fed7aa; border-radius: 8px;">
            <i class="fas fa-star" style="font-size: 2rem; color: #f59e0b; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #92400e;">VIP Code</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Giftcode dành cho thành viên VIP</p>
        </div>
        <div style="text-align: center; padding: 1.5rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
            <i class="fas fa-users" style="font-size: 2rem; color: #3b82f6; margin-bottom: 1rem;"></i>
            <h4 style="margin-bottom: 0.5rem; color: #1e40af;">Public Code</h4>
            <p style="color: #6b7280; font-size: 0.875rem;">Giftcode công khai cho mọi người</p>
        </div>
    </div>
</div>

<!-- Tips -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lightbulb"></i>
            Mẹo sử dụng Giftcode
        </h3>
    </div>
    <div class="grid grid-2">
        <div>
            <h4 style="color: #374151; margin-bottom: 1rem;">📱 Theo dõi Giftcode mới</h4>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Theo dõi Fanpage Facebook chính thức</li>
                <li>Tham gia group Discord/Telegram</li>
                <li>Đăng ký nhận thông báo email</li>
                <li>Kiểm tra website thường xuyên</li>
            </ul>
        </div>
        <div>
            <h4 style="color: #374151; margin-bottom: 1rem;">⚡ Sử dụng hiệu quả</h4>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li>Nhập giftcode ngay khi có</li>
                <li>Kiểm tra hạn sử dụng</li>
                <li>Đảm bảo tài khoản đã liên kết</li>
                <li>Lưu ý điều kiện sử dụng</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
// Auto uppercase giftcode input
document.getElementById('giftcode').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Load active giftcodes
function loadActiveGiftcodes() {
    // Mock data for now
    const giftcodes = [
        {
            code: 'WELCOME2025',
            name: 'Giftcode chào mừng năm mới',
            rewards: ['1000 Coin', '5 Jewel of Bless', '10 Jewel of Soul'],
            expires_at: '2025-02-28',
            max_uses: 1000,
            used_count: 234,
            type: 'public'
        },
        {
            code: 'VIPONLY123',
            name: 'Giftcode dành cho VIP',
            rewards: ['5000 Coin', '1 Jewel of Life', '20 Jewel of Chaos'],
            expires_at: '2025-01-31',
            max_uses: 100,
            used_count: 67,
            type: 'vip'
        },
        {
            code: 'EVENT2025',
            name: 'Sự kiện Tết Nguyên Đán',
            rewards: ['2000 Coin', '10 Jewel of Bless', '1 Box of Luck'],
            expires_at: '2025-02-15',
            max_uses: 500,
            used_count: 123,
            type: 'event'
        }
    ];

    const container = document.getElementById('activeGiftcodes');
    let html = '<div style="display: flex; flex-direction: column; gap: 1rem;">';

    giftcodes.forEach(gift => {
        const typeColors = {
            'public': { bg: '#f0f9ff', border: '#bae6fd', text: '#1e40af' },
            'vip': { bg: '#fef3c7', border: '#fed7aa', text: '#92400e' },
            'event': { bg: '#f0fdf4', border: '#bbf7d0', text: '#166534' }
        };

        const colors = typeColors[gift.type] || typeColors.public;
        const progress = (gift.used_count / gift.max_uses) * 100;

        html += `
            <div style="background: ${colors.bg}; border: 1px solid ${colors.border}; border-radius: 12px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 1.25rem; font-weight: 600; color: ${colors.text}; margin-bottom: 0.5rem;">
                            ${gift.code}
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">
                            ${gift.name}
                        </div>
                        <div style="color: #6b7280; font-size: 0.75rem;">
                            Hết hạn: ${new Date(gift.expires_at).toLocaleDateString('vi-VN')}
                        </div>
                    </div>
                    <button onclick="copyGiftcode('${gift.code}')" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.5rem 1rem;">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Phần thưởng:</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        ${gift.rewards.map(reward => `
                            <span style="background: rgba(255,255,255,0.7); padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; color: #374151;">
                                ${reward}
                            </span>
                        `).join('')}
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="font-size: 0.75rem; color: #6b7280;">Đã sử dụng:</span>
                        <span style="font-size: 0.75rem; color: #6b7280;">${gift.used_count}/${gift.max_uses}</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.5); border-radius: 9999px; height: 0.5rem;">
                        <div style="background: ${colors.text}; height: 100%; border-radius: 9999px; width: ${progress}%;"></div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}

// Load giftcode history
function loadGiftcodeHistory() {
    @if(session('user_account.game_account_id'))
    // Mock data for now
    const history = [
        {
            code: 'NEWBIE2024',
            used_at: '2025-01-15 14:30:00',
            rewards: ['500 Coin', '3 Jewel of Bless'],
            status: 'success'
        },
        {
            code: 'CHRISTMAS24',
            used_at: '2024-12-25 10:15:00',
            rewards: ['1000 Coin', '1 Box of Luck'],
            status: 'success'
        }
    ];

    const container = document.getElementById('giftcodeHistory');

    if (history.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; color: #6b7280; padding: 2rem;">
                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Chưa sử dụng giftcode nào</p>
            </div>
        `;
        return;
    }

    let html = '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;">';
    html += `
        <thead>
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Mã code</th>
                <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Phần thưởng</th>
                <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Thời gian</th>
                <th style="text-align: left; padding: 0.75rem; font-weight: 500; color: #374151;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
    `;

    history.forEach(item => {
        html += `
            <tr style="border-bottom: 1px solid #f3f4f6;">
                <td style="padding: 0.75rem; font-family: monospace; font-weight: 500;">${item.code}</td>
                <td style="padding: 0.75rem;">
                    ${item.rewards.map(reward => `
                        <span style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-right: 0.25rem;">
                            ${reward}
                        </span>
                    `).join('')}
                </td>
                <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                    ${new Date(item.used_at).toLocaleString('vi-VN')}
                </td>
                <td style="padding: 0.75rem;">
                    <span class="status-badge status-completed">Thành công</span>
                </td>
            </tr>
        `;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
    @endif
}

// Copy giftcode function
function copyGiftcode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show temporary success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.style.background = '#10b981';
        btn.style.color = 'white';

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}

// Form submission
document.getElementById('giftcodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('giftcode').value;
    if (code) {
        alert(`Chức năng nhập giftcode "${code}" đang được phát triển!`);
        // Reset form
        this.reset();
    }
});

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadActiveGiftcodes();
    loadGiftcodeHistory();
});
@endsection
