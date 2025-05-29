@extends('layouts.user')

@section('title', 'Lịch sử giao dịch - MU Game Portal')

@section('content')
<!-- Filter Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Bộ lọc
        </h3>
    </div>
    <form method="GET" action="{{ route('user.recharge.history') }}" id="filterForm">
        <div class="grid grid-4">
            <div class="form-group">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="method" class="form-label">Phương thức</label>
                <select name="method" id="method" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Thẻ cào</option>
                    <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                    <option value="paypal" {{ request('method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date_from" class="form-label">Từ ngày</label>
                <input 
                    type="date" 
                    name="date_from" 
                    id="date_from" 
                    class="form-input" 
                    value="{{ request('date_from') }}"
                >
            </div>
            
            <div class="form-group">
                <label for="date_to" class="form-label">Đến ngày</label>
                <input 
                    type="date" 
                    name="date_to" 
                    id="date_to" 
                    class="form-input" 
                    value="{{ request('date_to') }}"
                >
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Lọc kết quả
            </button>
            <a href="{{ route('user.recharge.history') }}" class="btn btn-outline">
                <i class="fas fa-times"></i>
                Xóa bộ lọc
            </a>
        </div>
    </form>
</div>

<!-- Summary Stats -->
<div class="grid grid-4">
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.5rem;">
                {{ $payments->total() }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Tổng giao dịch</div>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem;">
                {{ $payments->where('status', 'completed')->count() }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Hoàn thành</div>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
                {{ $payments->where('status', 'pending')->count() }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Chờ xử lý</div>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-bottom: 0.5rem;">
                {{ $payments->where('status', 'rejected')->count() }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Từ chối</div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Lịch sử giao dịch
        </h3>
    </div>
    
    @if($payments->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Mã GD</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Phương thức</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Số tiền</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Coin</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Trạng thái</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: #374151;">Thời gian</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: #374151;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr style="border-bottom: 1px solid #f3f4f6; {{ $payment->isPending() ? 'background: #fefce8;' : '' }}">
                            <td style="padding: 1rem;">
                                <div style="font-family: monospace; font-weight: 600;">#{{ $payment->id }}</div>
                                @if($payment->transaction_ref)
                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $payment->transaction_ref }}</div>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.25rem;">{{ $payment->getPaymentMethodIcon() }}</span>
                                    <span>{{ $payment->getPaymentMethodText() }}</span>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #374151;">{{ number_format($payment->amount) }}đ</div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #f59e0b;">{{ number_format($payment->coins_requested) }}</div>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="status-badge {{ $payment->getStatusBadgeClass() }}">
                                    {{ $payment->getStatusText() }}
                                </span>
                                @if($payment->admin_notes)
                                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                                        {{ Str::limit($payment->admin_notes, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <div style="color: #6b7280; font-size: 0.875rem;">
                                    {{ $payment->created_at->format('d/m/Y') }}
                                </div>
                                <div style="color: #6b7280; font-size: 0.75rem;">
                                    {{ $payment->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="{{ route('user.recharge.show', $payment->id) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.5rem 1rem;">
                                    <i class="fas fa-eye"></i>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    @else
        <div style="text-align: center; color: #6b7280; padding: 3rem;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <h3 style="margin-bottom: 0.5rem;">Không có giao dịch nào</h3>
            <p style="margin-bottom: 2rem;">
                @if(request()->hasAny(['status', 'method', 'date_from', 'date_to']))
                    Không tìm thấy giao dịch nào phù hợp với bộ lọc.
                @else
                    Bạn chưa thực hiện giao dịch nào.
                @endif
            </p>
            <a href="{{ route('user.recharge') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nạp coin đầu tiên
            </a>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Thao tác nhanh
        </h3>
    </div>
    <div class="grid grid-3">
        <a href="{{ route('user.recharge') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nạp thêm coin
        </a>
        <button onclick="exportHistory()" class="btn btn-outline">
            <i class="fas fa-download"></i>
            Xuất lịch sử
        </button>
        <button onclick="refreshPage()" class="btn btn-outline">
            <i class="fas fa-sync-alt"></i>
            Làm mới
        </button>
    </div>
</div>

<!-- Help Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-question-circle"></i>
            Trạng thái giao dịch
        </h3>
    </div>
    <div class="grid grid-2">
        <div>
            <h4 style="color: #374151; margin-bottom: 1rem;">Ý nghĩa trạng thái:</h4>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-badge status-pending">Chờ xử lý</span>
                    <span style="color: #6b7280; font-size: 0.875rem;">Giao dịch đang chờ admin xử lý</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-badge status-processing">Đang xử lý</span>
                    <span style="color: #6b7280; font-size: 0.875rem;">Admin đang kiểm tra và xử lý</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-badge status-completed">Hoàn thành</span>
                    <span style="color: #6b7280; font-size: 0.875rem;">Coin đã được cộng vào tài khoản</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-badge status-rejected">Từ chối</span>
                    <span style="color: #6b7280; font-size: 0.875rem;">Giao dịch bị từ chối (xem ghi chú)</span>
                </div>
            </div>
        </div>
        <div>
            <h4 style="color: #374151; margin-bottom: 1rem;">Thời gian xử lý:</h4>
            <ul style="color: #6b7280; font-size: 0.875rem; margin: 0; padding-left: 1rem;">
                <li><strong>Thẻ cào:</strong> 5-10 phút (tự động)</li>
                <li><strong>Chuyển khoản:</strong> 10-30 phút (thủ công)</li>
                <li><strong>PayPal:</strong> 5-15 phút (tự động)</li>
                <li><strong>Giờ làm việc:</strong> 8:00 - 22:00 hàng ngày</li>
            </ul>
            <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                <div style="font-weight: 500; color: #92400e; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Lưu ý
                </div>
                <div style="color: #92400e; font-size: 0.875rem;">
                    Nếu giao dịch quá 1 giờ chưa được xử lý, vui lòng liên hệ admin.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
function exportHistory() {
    alert('Chức năng xuất lịch sử đang được phát triển!');
}

function refreshPage() {
    window.location.reload();
}

// Auto refresh pending transactions every 30 seconds
setInterval(function() {
    const pendingRows = document.querySelectorAll('tr[style*="background: #fefce8"]');
    if (pendingRows.length > 0) {
        // In a real implementation, this would make an AJAX call to check status
        console.log('Checking pending transactions...');
    }
}, 30000);

// Set max date to today
document.getElementById('date_from').max = new Date().toISOString().split('T')[0];
document.getElementById('date_to').max = new Date().toISOString().split('T')[0];

// Auto submit form when date changes
document.getElementById('date_from').addEventListener('change', function() {
    if (this.value && document.getElementById('date_to').value) {
        document.getElementById('filterForm').submit();
    }
});

document.getElementById('date_to').addEventListener('change', function() {
    if (this.value && document.getElementById('date_from').value) {
        document.getElementById('filterForm').submit();
    }
});
@endsection
