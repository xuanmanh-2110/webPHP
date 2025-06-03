@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <button onclick="history.back()" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
        ← Quay lại trang trước
    </button>
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Danh sách khách hàng</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tên</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Số điện thoại</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
            @foreach ($customers as $customer)
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->phone }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('customers.orders', $customer) }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200 text-center">Lịch sử đơn hàng</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

@endsection
