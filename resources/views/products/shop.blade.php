<a href="{{ url('/') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">Quay lại trang chính</a>

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-5 p-4">
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-rose-600 text-3xl font-bold">Sản phẩm nổi bật</h2>
        <a href="{{ route('cart.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors duration-200 flex items-center">
            <span class="mr-2">🛒</span>Xem giỏ hàng
        </a>
    </div>
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
                <img src="{{ $p->image ? asset('images/products/' . $p->image) : 'https://via.placeholder.com/200x200' }}" alt="{{ $p->name }}" class="w-[200px] h-[200px] object-cover rounded-md mb-3 border border-gray-200 transition-transform duration-300 group-hover:scale-105">
                <div class="text-base font-semibold mb-2 text-rose-600 min-h-[4.5rem] flex items-center justify-center leading-tight w-full">{{ $p->name }}</div>
                <div class="mb-3">
                    @if(in_array($p->id, $latestProductIds))
                        {{-- Sản phẩm trong top 5 mới nhất - có giảm giá --}}
                        <span class="text-sm text-gray-500 line-through mr-2">{{ number_format($p->price, 0, ',', '.') }} VND</span>
                        <br>
                        <span class="text-rose-600 text-lg font-bold">{{ number_format($p->price * 0.8, 0, ',', '.') }} VND</span>
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs ml-2">-20%</span>
                    @else
                        {{-- Sản phẩm thông thường - không giảm giá --}}
                        <span class="text-rose-600 text-lg font-bold">{{ number_format($p->price, 0, ',', '.') }} VND</span>
                    @endif
                </div>
            </a>
            <div class="flex flex-col gap-1.5 w-full mt-2">
                <form action="{{ route('cart.add', $p->id) }}" method="POST" class="w-full add-to-cart-form">
                    @csrf
                    <button type="submit" class="w-full p-2 rounded-md text-sm cursor-pointer transition-all duration-300 flex items-center justify-center font-semibold bg-rose-500 text-white hover:bg-rose-600 hover:translate-y-[-2px] hover:shadow-md">
                        <span class="mr-1.5">🛒</span>Thêm vào giỏ
                    </button>
                </form>
                <form action="{{ route('cart.buyNow', $p->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full p-2 rounded-md text-sm cursor-pointer transition-all duration-300 flex items-center justify-center font-semibold bg-rose-500 text-white shadow-md hover:bg-rose-600 hover:translate-y-[-2px] hover:shadow-lg">
                        <span class="mr-1.5">⚡</span>Mua ngay
                    </button>
                </form>
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
<!-- Thông báo Toast -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 cursor-pointer hidden">
    <div class="flex items-center justify-between">
        <span id="toast-message"></span>
        <button id="toast-close" class="ml-4 text-white hover:text-gray-200 font-bold text-lg">&times;</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form thêm vào giỏ hàng bằng AJAX
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.action;
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại!');
            });
        });
    });
    
    // Xử lý nút đóng toast
    const toastCloseBtn = document.getElementById('toast-close');
    const toast = document.getElementById('toast');
    
    if (toastCloseBtn && toast) {
        toastCloseBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Ngăn event bubble lên toast
            hideToast();
        });
        
        // Cũng có thể ấn vào toàn bộ toast để đóng
        toast.addEventListener('click', function() {
            hideToast();
        });
    }
    
    function hideToast() {
        const toast = document.getElementById('toast');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 300);
    }
    
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        toast.classList.remove('translate-x-full');
        
        setTimeout(() => {
            hideToast();
        }, 3000);
    }
});
</script>