@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
<a href="{{ route('products.shop') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">&larr; Quay lại cửa hàng</a>


    <div class="bg-white rounded-xl shadow-lg overflow-hidden md:flex transition-all duration-300 ease-in-out transform hover:scale-[1.01] hover:shadow-xl">
        <div class="md:flex-shrink-0 md:w-1/2 p-6">
            @if($product->image)
                <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" class="w-[600px] h-[400px] object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105">
            @else
                <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No Image" class="w-[600px] h-[400px] object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105">
            @endif
        </div>
        <div class="p-6 md:w-1/2 flex flex-col justify-between">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-3">{{ $product->name }}</h1>
                <p class="text-3xl text-rose-600 font-bold mb-6">{{ number_format($product->price, 0, ',', '.') }} VND</p>
                <div class="prose prose-lg text-gray-700 mb-8 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 mt-auto">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full px-8 py-4 bg-rose-500 text-white font-semibold rounded-lg text-lg shadow-md hover:bg-rose-600 transition-all duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-opacity-50">Thêm vào giỏ hàng</button>
                </form>
                <form action="{{ route('cart.buyNow', $product->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full px-8 py-4 bg-gray-200 text-gray-800 font-semibold rounded-lg text-lg shadow-md hover:bg-gray-300 transition-all duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">Mua ngay</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sản phẩm liên quan --}}
    <section class="mt-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Sản phẩm liên quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($randomProducts as $p)
                <a href="{{ route('products.show', $p->id) }}" class="bg-white rounded-lg shadow-md transition-all duration-300 ease-in-out relative overflow-hidden border border-gray-200 flex flex-col items-center h-full hover:translate-y-[-5px] hover:shadow-lg group no-underline text-inherit">
                    <div class="relative w-full h-48">
                        @if($p->image)
                            <img src="{{ asset('images/products/' . $p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105">
                        @else
                            <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No Image" class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105">
                        @endif
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate">{{ $p->name }}</h3>
                        <p class="text-xl font-bold text-rose-600">{{ number_format($p->price, 0, ',', '.') }} VND</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButtons = document.querySelectorAll('button[type="submit"]');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const form = this.closest('form');
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Cập nhật số lượng giỏ hàng nếu muốn
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.');
                });
            });
        });
    });
</script>
@endsection
