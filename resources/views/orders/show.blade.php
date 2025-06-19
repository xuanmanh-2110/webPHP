@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto my-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Chi tiết đơn hàng #{{ $order->id }}</h2>
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <p class="mb-2"><strong class="font-semibold text-gray-800">Khách hàng:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold text-gray-800">Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold text-gray-800">Số điện thoại:</strong> {{ $order->phone ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold text-gray-800">Địa chỉ giao hàng:</strong> {{ $order->address ?? 'N/A' }}</p>
        <p class="mb-4"><strong class="font-semibold text-gray-800">Ngày đặt:</strong> {{ $order->formatted_created_at }}</p>
        <h4 class="text-xl font-semibold text-gray-800 mb-4">Sản phẩm</h4>
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->price, 2) }}₫</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->price * $item->quantity, 2) }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right text-xl font-bold text-rose-600 mb-4">
            <strong>Tổng cộng: {{ number_format($order->total_amount, 2) }}₫</strong>
        </div>
        <div class="mt-6">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : ($order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">Trạng thái:
                @if($order->status === 'pending')
                    Đang chờ xử lý
                @elseif($order->status === 'processing')
                    Đang xử lý
                @elseif($order->status === 'shipped')
                    Đã giao hàng
                @elseif($order->status === 'cancelled')
                    Đã hủy
                @else
                    {{ $order->status }}
                @endif
            </span>
        </div>
    </div>
</div>
<div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
    <button onclick="window.history.back()" class="w-full sm:w-auto px-8 py-3 bg-rose-100 text-rose-600 border-none rounded-md text-lg cursor-pointer transition-colors duration-200 hover:bg-rose-200">
        ← Quay lại trang trước
    </button>
    <a href="{{ url('/') }}" class="w-full sm:w-auto">
        <button class="w-full px-8 py-3 bg-rose-100 text-rose-600 border-none rounded-md text-lg cursor-pointer transition-colors duration-200 hover:bg-rose-200">
            Về trang chính
        </button>
    </a>
    @if(($order->status ?? 'pending') === 'pending' || ($order->status ?? 'pending') === 'processing')
    <form id="cancel-order-form" action="{{ route('orders.cancel', $order->id) }}" method="POST" class="w-full sm:w-auto">
        @csrf
        <button type="button" id="cancel-order-btn" class="w-full px-8 py-3 bg-red-500 hover:bg-red-600 text-white border-none rounded-md text-lg cursor-pointer transition-colors duration-200">
            Hủy đơn hàng
        </button>
    </form>
    @endif
</div>

<!-- Modal xác nhận hủy đơn hàng -->
<div id="confirm-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-rose-100 rounded-full">
                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Xác nhận hủy đơn hàng</h3>
            <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.</p>
            <div class="flex space-x-3">
                <button id="confirm-cancel" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Hủy
                </button>
                <button id="confirm-delete" class="flex-1 px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-colors duration-200">
                    Hủy đơn hàng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancel-order-btn');
    const modal = document.getElementById('confirm-modal');
    const confirmBtn = document.getElementById('confirm-delete');
    const cancelModalBtn = document.getElementById('confirm-cancel');
    const form = document.getElementById('cancel-order-form');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal();
        });
    }
    
    function showModal() {
        modal.classList.remove('hidden');
        
        // Xử lý khi nhấn Hủy
        const handleCancel = () => {
            modal.classList.add('hidden');
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelModalBtn.removeEventListener('click', handleCancel);
        };
        
        // Xử lý khi nhấn Hủy đơn hàng
        const handleConfirm = () => {
            modal.classList.add('hidden');
            if (form) {
                form.submit();
            }
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelModalBtn.removeEventListener('click', handleCancel);
        };
        
        // Thêm event listeners
        confirmBtn.addEventListener('click', handleConfirm);
        cancelModalBtn.addEventListener('click', handleCancel);
    }
});
</script>

@endsection