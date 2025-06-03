@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <button onclick="history.back()" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
        ← Quay lại trang trước
    </button>
    <h1 class="text-3xl font-bold text-rose-600 mb-6 text-center">Danh sách đơn hàng</h1>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Khách hàng</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tổng tiền</th>
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
                            {{ number_format($order->items->sum(function($i){ return $i->price * $i->quantity; }), 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 text-center">Xem</a>
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 w-full" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
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
@endsection