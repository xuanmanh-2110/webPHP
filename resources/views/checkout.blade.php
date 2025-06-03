@extends('layouts.app')

@section('content')
<a href="http://127.0.0.1:8000/shop" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">← Quay lại cửa hàng</a>
<div class="max-w-3xl mx-auto my-10 p-6 bg-white rounded-lg">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Thanh toán đơn hàng</h2>
    <form action="{{ route('checkout.process') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-1">Họ tên khách hàng:</label>
            <input type="text" value="{{ $user->name }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" disabled>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-1">Địa chỉ giao hàng <span class="text-rose-500">*</span>:</label>
            <input type="text" name="address" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-1">Số điện thoại <span class="text-rose-500">*</span>:</label>
            <input type="text" name="phone" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
        </div>
        <h4 class="text-xl font-semibold text-gray-800 mb-4">Giỏ hàng của bạn</h4>
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
                    @foreach($cart as $id => $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['quantity'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item['price'], 2) }}₫</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item['price'] * $item['quantity'], 2) }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right text-xl font-bold text-rose-600 mb-4">
            <strong>Tổng cộng: {{ number_format($total, 2) }}₫</strong>
        </div>
        <button type="submit" class="w-full py-3 px-8 bg-rose-500 text-white border-none rounded-md text-lg cursor-pointer hover:bg-rose-600 transition-colors duration-200">Xác nhận đặt hàng</button>
    </form>
</div>
@endsection