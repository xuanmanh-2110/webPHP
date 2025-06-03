@extends('layouts.app')

@section('content')
<button onclick="window.history.back()" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
  ← Quay lại trang trước
</button>
<div class="max-w-4xl mx-auto my-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Lịch sử mua hàng</h2>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-white p-4 rounded-lg shadow-sm">
        <div class="col-span-1">
            <label for="status" class="block text-gray-700 text-sm font-medium mb-1">Trạng thái</label>
            <select name="status" id="status" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">
                <option value="">Tất cả</option>
                <option value="pending" {{ ($filters['status'] ?? '')=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="processing" {{ ($filters['status'] ?? '')=='processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipped" {{ ($filters['status'] ?? '')=='shipped' ? 'selected' : '' }}>Đã giao</option>
                <option value="cancelled" {{ ($filters['status'] ?? '')=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
        <div class="col-span-1">
            <label for="from" class="block text-gray-700 text-sm font-medium mb-1">Từ ngày</label>
            <input type="date" name="from" id="from" value="{{ $filters['from'] ?? '' }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">
        </div>
        <div class="col-span-1">
            <label for="to" class="block text-gray-700 text-sm font-medium mb-1">Đến ngày</label>
            <input type="date" name="to" id="to" value="{{ $filters['to'] ?? '' }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">
        </div>
        <div class="col-span-1 flex items-end">
            <button type="submit" class="w-full bg-rose-500 text-white hover:bg-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200">Lọc</button>
        </div>
    </form>
    @if($orders->count() == 0)
        <p class="text-gray-700 text-lg text-center">Không có đơn hàng nào.</p>
    @else
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ngày mua</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Mã đơn</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Sản phẩm</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Số lượng</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tổng tiền</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Trạng thái</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">#{{ $order->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach($order->items as $item)
                            <div>{{ $item->product->name ?? 'N/A' }}</div>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach($order->items as $item)
                            <div>{{ $item->quantity }}</div>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->total_amount, 2) }}₫</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($order->status == 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Chờ xử lý</span>
                        @elseif($order->status == 'processing')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Đang xử lý</span>
                        @elseif($order->status == 'shipped')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã giao</span>
                        @elseif($order->status == 'cancelled')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Đã hủy</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Khác</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('orders.show', $order->id) }}" class="text-rose-500 hover:text-rose-600">Xem chi tiết</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection