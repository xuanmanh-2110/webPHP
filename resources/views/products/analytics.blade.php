@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-7xl px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Thống kê sản phẩm: {{ $product->name }}</h1>
            <p class="text-gray-600 mt-2">Phân tích chi tiết về hiệu suất và đánh giá sản phẩm</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Quay lại danh sách
            </a>
            <a href="{{ route('products.show', $product->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Xem sản phẩm
            </a>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tổng đơn hàng</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="text-2xl">📦</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tổng doanh thu</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalRevenue, 0, ',', '.') }}₫</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="text-2xl">💰</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tổng đánh giá</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalReviews }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <span class="text-2xl">⭐</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-rose-500">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Đánh giá TB</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $averageRating ? number_format($averageRating, 1) : 'N/A' }}</p>
                </div>
                <div class="p-3 bg-rose-100 rounded-full">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Biểu đồ phân tích đánh giá -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Phân tích đánh giá</h2>
            <div class="space-y-4">
                @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $ratingDistribution[$i] ?? 0;
                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                @endphp
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700 w-12">{{ $i }} sao</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-4">
                            <div class="bg-yellow-400 h-4 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-600 w-12">{{ $count }}</span>
                    <span class="text-sm text-gray-500 w-16">({{ number_format($percentage, 1) }}%)</span>
                </div>
                @endfor
            </div>
        </div>

        <!-- Thống kê mua hàng -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Thống kê mua hàng</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="font-medium text-gray-700">Tỉ lệ mua lại</span>
                    <span class="text-lg font-bold text-blue-600">{{ number_format($repurchaseRate, 1) }}%</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="font-medium text-gray-700">Số lượng bán TB/tháng</span>
                    <span class="text-lg font-bold text-green-600">{{ number_format($avgMonthlyOrders, 1) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="font-medium text-gray-700">Tỉ lệ có đánh giá</span>
                    <span class="text-lg font-bold text-yellow-600">{{ number_format($reviewRate, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đánh giá chi tiết -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Đánh giá chi tiết</h2>
        <div class="space-y-4">
            @forelse($reviews as $review)
            <div class="border-b border-gray-200 pb-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200">
                            @if($review->user && $review->user->avatar)
                                <img src="{{ asset('images/avatars/' . $review->user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-rose-100 flex items-center justify-center">
                                    <span class="text-rose-600 font-semibold">{{ substr($review->reviewer_name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $review->reviewer_name }}</p>
                            <div class="flex items-center space-x-2">
                                <div class="flex text-yellow-400">
                                    <span>{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($review->user_id)
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Đã xác thực</span>
                    @else
                    <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Khách vãng lai</span>
                    @endif
                </div>
                <p class="text-gray-700 mt-2 ml-13">{{ $review->content }}</p>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@endsection