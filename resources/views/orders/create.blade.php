@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Tạo đơn hàng</h2>
    <form method="POST" action="{{ route('orders.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="mb-4">
            <label for="customer_id" class="block text-gray-700 text-sm font-medium mb-1">Chọn khách hàng:</label>
            <select name="customer_id" id="customer_id" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <h4 class="text-xl font-semibold text-gray-800 mb-4">Chọn hoa và số lượng:</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach ($products as $index => $product)
                <div class="flex items-center space-x-2 p-3 border border-gray-200 rounded-md shadow-sm">
                    <input type="checkbox" name="products[]" value="{{ $product->id }}" id="product_{{ $product->id }}" class="form-checkbox h-5 w-5 text-rose-500 transition duration-150 ease-in-out">
                    <label for="product_{{ $product->id }}" class="text-gray-700 flex-grow">{{ $product->name }} - {{ number_format($product->price) }}đ</label>
                    <input type="number" name="quantities[]" value="1" min="1" class="w-16 sm:w-20 p-1 border border-gray-300 rounded-md text-sm">
                </div>
            @endforeach
        </div>

        <button type="submit" class="bg-rose-500 text-white hover:bg-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200">Tạo đơn hàng</button>
    </form>
</div>
@endsection
