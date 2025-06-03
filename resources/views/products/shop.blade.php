<a href="{{ url('/') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">Quay lại trang chính</a>

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-5 p-4">
    <h2 class="mb-5 text-rose-600 text-center text-3xl font-bold">Sản phẩm nổi bật</h2>
    <form method="GET" action="{{ route('products.shop') }}" class="mb-5 p-3 bg-white rounded-lg shadow-md">
        <div class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm kiếm sản phẩm..." class="p-2 rounded-md border border-gray-300 bg-white flex-1 min-w-[150px] focus:outline-none focus:ring-2 focus:ring-rose-500">
            <select name="category" class="p-2 rounded-md border border-gray-300 bg-white min-w-[120px] focus:outline-none focus:ring-2 focus:ring-rose-500">
                <option value="">--Danh mục--</option>
                @if(isset($categoryList))
                    @foreach($categoryList as $cat)
                        <option value="{{ $cat }}" {{ (isset($category) && $category == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                @endif
            </select>
            <select name="price" class="p-2 rounded-md border border-gray-300 bg-white min-w-[120px] focus:outline-none focus:ring-2 focus:ring-rose-500">
                <option value="">--Khoảng giá--</option>
                <option value="1" {{ (isset($price) && $price=='1') ? 'selected' : '' }}>< 200.000₫</option>
                <option value="2" {{ (isset($price) && $price=='2') ? 'selected' : '' }}>200.000₫ - 500.000₫</option>
                <option value="3" {{ (isset($price) && $price=='3') ? 'selected' : '' }}>> 500.000₫</option>
            </select>
            <select name="sort" class="p-2 rounded-md border border-gray-300 bg-white min-w-[120px] focus:outline-none focus:ring-2 focus:ring-rose-500">
                <option value="">Sắp xếp mặc định</option>
                <option value="price_asc" {{ (isset($sort) && $sort=='price_asc') ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ (isset($sort) && $sort=='price_desc') ? 'selected' : '' }}>Giá giảm dần</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-md bg-rose-500 text-white font-semibold whitespace-nowrap hover:bg-rose-600 transition-all duration-300 w-auto">Lọc</button>
            @if(!empty($search))
                <span class="ml-2 text-rose-500 text-sm w-auto text-left">Kết quả cho: <strong class="font-semibold">{{ $search }}</strong></span>
            @endif
        </div>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 p-0">
        @forelse($products as $p)
        <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 ease-in-out relative overflow-hidden border border-gray-200 flex flex-col items-center h-full hover:translate-y-[-5px] hover:shadow-lg">
            <a href="{{ route('products.show', $p->id) }}" class="no-underline text-inherit">
                <img src="{{ $p->image ? asset('images/products/' . $p->image) : 'https://via.placeholder.com/250x250' }}" alt="{{ $p->name }}" class="w-full h-56 object-cover rounded-md mb-3 border border-gray-200 transition-transform duration-300 group-hover:scale-105">
                <div class="text-base font-semibold mb-2 text-rose-600 min-h-[4.5rem] flex items-center justify-center leading-tight w-full">{{ $p->name }}</div>
                <div class="text-rose-600 text-lg font-bold mb-3">{{ number_format($p->price, 0, ',', '.') }} VND</div>
            </a>
            <div class="flex flex-col gap-1.5 w-full mt-2">
                <form action="{{ route('cart.add', $p->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full p-2 rounded-md text-sm cursor-pointer transition-all duration-300 flex items-center justify-center font-semibold bg-rose-500 text-white hover:bg-rose-600 hover:translate-y-[-2px] hover:shadow-md">
                        <span class="mr-1.5">🛒</span>Thêm vào giỏ
                    </button>
                </form>
                <button class="w-full p-2 rounded-md text-sm cursor-pointer transition-all duration-300 flex items-center justify-center font-semibold bg-rose-500 text-white shadow-md hover:bg-rose-600 hover:translate-y-[-2px] hover:shadow-lg" onclick="window.location='{{ route('checkout.show', ['selected' => $p->id]) }}'">
                    <span class="mr-1.5">⚡</span>Mua ngay
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center p-8 text-rose-500">Không có sản phẩm phù hợp.</div>
        @endforelse
    </div>
    <div class="mt-8 flex justify-center">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endsection