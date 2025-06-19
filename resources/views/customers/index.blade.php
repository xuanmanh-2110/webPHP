@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <a href="{{ url('/') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
        ← Quay về trang chính
    </a>
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Danh sách khách hàng</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($customers as $customer)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                <!-- Thông tin khách hàng -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin khách hàng #{{ $customer->id }}</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="font-medium text-gray-600">Họ và tên:</span>
                            <p class="text-gray-800">{{ $customer->user ? $customer->user->name : $customer->name }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Email:</span>
                            <p class="text-gray-800">{{ $customer->user ? $customer->user->email : $customer->email }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Số điện thoại:</span>
                            <p class="text-gray-800">{{ $customer->user ? ($customer->user->phone ?: 'Chưa cập nhật') : ($customer->phone ?: 'Chưa cập nhật') }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Địa chỉ:</span>
                            <p class="text-gray-800">{{ $customer->user ? ($customer->user->address ?: 'Chưa cập nhật') : ($customer->address ?: 'Chưa cập nhật') }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Ngày tham gia:</span>
                            <p class="text-gray-800">{{ $customer->user ? $customer->user->created_at->format('d/m/Y') : $customer->created_at->format('d/m/Y') }}</p>
                        </div>
                        @if($customer->user)
                            <div class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                                Đã liên kết với tài khoản user
                            </div>
                        @else
                            <div class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded">
                                Chưa liên kết với tài khoản user
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hành động -->
                <div class="border-t pt-4">
                    <a href="{{ route('customers.orders', $customer) }}"
                       class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 px-4 rounded transition-colors duration-200 text-center inline-block">
                        Lịch sử đơn hàng
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

@endsection
