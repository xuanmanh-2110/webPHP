@extends('layouts.app')

@section('content')
<button onclick="window.history.back()" class="mt-4 mb-3 px-4 py-2 bg-rose-100 text-rose-600 border-none rounded-md text-base cursor-pointer hover:bg-rose-200 transition-colors duration-200">
  ← Quay lại trang trước
</button>
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Lịch sử mua hàng của khách hàng: {{ $customer->name }}</h2>

    @if ($orders->isEmpty())
        <p class="text-gray-700 text-lg text-center">Khách hàng này chưa có đơn hàng nào.</p>
    @else
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Mã đơn hàng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tổng tiền</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ngày đặt</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->total_amount) }} đ</td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
