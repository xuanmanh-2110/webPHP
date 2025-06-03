@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto my-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Chi tiết đơn hàng #{{ $order->id }}</h2>
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <p class="mb-2"><strong class="font-semibold text-gray-800">Khách hàng:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold text-gray-800">Địa chỉ giao hàng:</strong> {{ $order->address ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold text-gray-800">Số điện thoại:</strong> {{ $order->phone ?? 'N/A' }}</p>
        <p class="mb-4"><strong class="font-semibold text-gray-800">Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
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
    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');" class="w-full sm:w-auto">
        @csrf
        <button type="submit" class="w-full px-8 py-3 bg-red-500 hover:bg-red-600 text-white border-none rounded-md text-lg cursor-pointer transition-colors duration-200">
            Hủy đơn hàng
        </button>
    </form>
    @endif
</div>
@endsection