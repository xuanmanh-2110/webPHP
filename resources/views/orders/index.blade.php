@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <a href="{{ url('/') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
        ← Quay về trang chính
    </a>
    <h1 class="text-3xl font-bold text-rose-600 mb-6 text-center">Danh sách đơn hàng</h1>

    <!-- Thông báo -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Bộ lọc trạng thái -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2">
                <label for="status" class="text-sm font-medium text-gray-700">Lọc theo trạng thái:</label>
                <select name="status" id="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    @foreach(\App\Models\Order::getStatuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label for="from" class="text-sm font-medium text-gray-700">Từ ngày:</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            <div class="flex items-center gap-2">
                <label for="to" class="text-sm font-medium text-gray-700">Đến ngày:</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                Lọc
            </button>
            @if(request()->hasAny(['status', 'from', 'to']))
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                    Xóa bộ lọc
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Khách hàng</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tổng tiền</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ngày tạo</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ number_format($order->total_amount ?? $order->items->sum(function($i){ return $i->price * $i->quantity; }), 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                                {{ $order->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">{{ $order->formatted_created_at }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 text-center">Xem</a>
                                
                                <!-- Nút xác nhận đơn hàng -->
                                @if($order->status === 'pending')
                                <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="inline-block w-full sm:w-auto confirm-order-form">
                                    @csrf
                                    <input type="hidden" name="status" value="processing">
                                    <button type="button" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 w-full confirm-order-btn">
                                        Xác nhận đơn hàng
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block w-full sm:w-auto delete-order-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 w-full delete-order-btn">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

<!-- Modal xác nhận xóa đơn hàng -->
<div id="confirm-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-rose-100 rounded-full">
                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Xác nhận xóa đơn hàng</h3>
            <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn xóa đơn hàng này? Hành động này không thể hoàn tác.</p>
            <div class="flex space-x-3">
                <button id="confirm-cancel" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Hủy
                </button>
                <button id="confirm-delete" class="flex-1 px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-colors duration-200">
                    Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận đơn hàng -->
<div id="confirm-order-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Xác nhận đơn hàng</h3>
            <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn xác nhận đơn hàng này? Đơn hàng sẽ chuyển sang trạng thái "Đang xử lý".</p>
            <div class="flex space-x-3">
                <button id="confirm-order-cancel" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Hủy
                </button>
                <button id="confirm-order-confirm" class="flex-1 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirm-modal');
    const confirmBtn = document.getElementById('confirm-delete');
    const cancelBtn = document.getElementById('confirm-cancel');
    
    const confirmOrderModal = document.getElementById('confirm-order-modal');
    const confirmOrderBtn = document.getElementById('confirm-order-confirm');
    const confirmOrderCancelBtn = document.getElementById('confirm-order-cancel');
    
    let currentForm = null;
    let currentConfirmForm = null;
    
    // Xử lý click nút xóa
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-order-btn')) {
            e.preventDefault();
            currentForm = e.target.closest('.delete-order-form');
            showDeleteModal();
        }
        
        // Xử lý click nút xác nhận đơn hàng
        if (e.target.classList.contains('confirm-order-btn')) {
            e.preventDefault();
            currentConfirmForm = e.target.closest('.confirm-order-form');
            showConfirmOrderModal();
        }
    });
    
    function showDeleteModal() {
        modal.classList.remove('hidden');
        
        const handleCancel = () => {
            modal.classList.add('hidden');
            currentForm = null;
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
        };
        
        const handleConfirm = () => {
            modal.classList.add('hidden');
            if (currentForm) {
                currentForm.submit();
            }
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
        };
        
        confirmBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);
    }
    
    function showConfirmOrderModal() {
        confirmOrderModal.classList.remove('hidden');
        
        const handleCancel = () => {
            confirmOrderModal.classList.add('hidden');
            currentConfirmForm = null;
            confirmOrderBtn.removeEventListener('click', handleConfirm);
            confirmOrderCancelBtn.removeEventListener('click', handleCancel);
        };
        
        const handleConfirm = () => {
            confirmOrderModal.classList.add('hidden');
            if (currentConfirmForm) {
                currentConfirmForm.submit();
            }
            confirmOrderBtn.removeEventListener('click', handleConfirm);
            confirmOrderCancelBtn.removeEventListener('click', handleCancel);
        };
        
        confirmOrderBtn.addEventListener('click', handleConfirm);
        confirmOrderCancelBtn.addEventListener('click', handleCancel);
    }
});

// Cập nhật thời gian thực
function refreshOrders() {
    const currentUrl = new URL(window.location.href);
    const params = new URLSearchParams(currentUrl.search);
    
    // Thêm tham số để chỉ lấy dữ liệu JSON
    const ajaxUrl = currentUrl.pathname + '?' + params.toString() + '&ajax=1';
    
    fetch(ajaxUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            // Cập nhật nội dung bảng
            document.querySelector('.overflow-x-auto').innerHTML = data.html;
            
            // Cập nhật thời gian hiển thị
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN');
            
            // Hiển thị thông báo cập nhật
            showUpdateNotification(`Cập nhật lúc ${timeString}`);
        }
    })
    .catch(error => {
        console.error('Lỗi khi cập nhật:', error);
    });
}

// Hiển thị thông báo cập nhật
function showUpdateNotification(message) {
    // Xóa thông báo cũ nếu có
    const oldNotification = document.querySelector('.update-notification');
    if (oldNotification) {
        oldNotification.remove();
    }
    
    // Tạo thông báo mới
    const notification = document.createElement('div');
    notification.className = 'update-notification fixed top-4 right-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded shadow-lg z-50';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Tự động cập nhật mỗi 30 giây
setInterval(refreshOrders, 30000);

</script>
@endsection