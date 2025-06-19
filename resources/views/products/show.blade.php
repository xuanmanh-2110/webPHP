@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-8 py-8">
    <div class="flex justify-between items-center mb-4">
        <button onclick="history.back()" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 cursor-pointer border-none">&larr; Trang trước</button>
        <a href="{{ route('cart.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors duration-200 flex items-center">
            <span class="mr-2">🛒</span>Xem giỏ hàng
        </a>
    </div>


    <div class="bg-white rounded-xl shadow-lg overflow-hidden md:flex transition-all duration-300 ease-in-out transform hover:scale-[1.01] hover:shadow-xl">
        <div class="md:flex-shrink-0 md:w-1/2 p-6">
            @if($product->image)
                <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" class="w-[430px] h-[430px] object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105">
            @else
                <img src="https://via.placeholder.com/430x430?text=No+Image" alt="No Image" class="w-[430px] h-[430px] object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105">
            @endif
        </div>
        <div class="p-6 md:w-1/2 flex flex-col justify-between">
            <div class="mt-10">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-3">{{ $product->name }}</h1>
                @if($isLatestProduct)
                <div class="mb-6">
                    <span class="text-xl text-gray-500 line-through mr-3">{{ number_format($product->price, 0, ',', '.') }} VND</span>
                    <p class="text-3xl text-rose-600 font-bold inline">{{ number_format($product->price * 0.8, 0, ',', '.') }} VND</p>
                    <span class="bg-red-500 text-white px-3 py-1 rounded text-sm ml-3">-20%</span>
                </div>
                @else
                <p class="text-3xl text-rose-600 font-bold mb-6">{{ number_format($product->price, 0, ',', '.') }} VND</p>
                @endif
                
                {{-- Hiển thị điểm đánh giá trung bình --}}
                @if($totalReviews > 0)
                <div class="flex items-center space-x-2 mb-6">
                    <div class="flex text-yellow-400 text-lg">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($averageRating))
                                <span>★</span>
                            @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                                <span>★</span>
                            @else
                                <span class="text-gray-300">★</span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-lg font-semibold text-gray-800">{{ $averageRating }}/5</span>
                    <span class="text-gray-600">({{ $totalReviews }} {{ $totalReviews == 1 ? 'đánh giá' : 'đánh giá' }})</span>
                </div>
                @else
                <div class="flex items-center space-x-2 mb-6">
                    <div class="flex text-gray-300 text-lg">
                        <span>★★★★★</span>
                    </div>
                    <span class="text-gray-500">Chưa có đánh giá</span>
                </div>
                @endif
                <div class="prose prose-lg text-gray-700 mb-8 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            
            <!-- Chọn số lượng -->
            <div class="flex items-center justify-start mb-0 mt-20">
                <label for="quantity" class="text-lg font-semibold text-gray-700 mr-4">Số lượng:</label>
                <div class="flex items-center border border-gray-300 rounded-lg">
                    <button type="button" id="decrease-qty" class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-l-lg">
                        <span class="text-xl">−</span>
                    </button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" class="w-16 text-center py-2 border-0 focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" id="increase-qty" class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-r-lg">
                        <span class="text-xl">+</span>
                    </button>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full sm:w-auto add-to-cart-form">
                    @csrf
                    <input type="hidden" name="quantity" id="cart-quantity" value="1">
                    <button type="submit" class="w-full px-8 py-4 bg-rose-500 text-white font-semibold rounded-lg text-lg shadow-md hover:bg-rose-600 transition-all duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-opacity-50">Thêm vào giỏ hàng</button>
                </form>
                <form action="{{ route('cart.buyNow', $product->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="quantity" id="buy-quantity" value="1">
                    <button type="submit" class="w-full px-8 py-4 bg-gray-200 text-gray-800 font-semibold rounded-lg text-lg shadow-md hover:bg-gray-300 transition-all duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">Mua ngay</button>
                </form>
            </div>
        </div>
    </div>


    {{-- Sản phẩm liên quan --}}
    <section class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Sản phẩm liên quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-3 p-0">
            @foreach($randomProducts as $p)
            <div class="bg-white rounded-lg shadow-sm p-3 text-center transition-all duration-300 ease-in-out relative overflow-hidden border border-gray-200 flex flex-col items-center h-full hover:translate-y-[-3px] hover:shadow-md">
                <a href="{{ route('products.show', $p->id) }}" class="no-underline text-inherit">
                    <img src="{{ $p->image ? asset('images/products/' . $p->image) : 'https://via.placeholder.com/200x200' }}" alt="{{ $p->name }}" class="w-full aspect-square object-cover rounded-md mb-2 border border-gray-200 transition-transform duration-300 group-hover:scale-105">
                    <div class="text-sm font-semibold mb-1 text-rose-600 min-h-[3rem] flex items-center justify-center leading-tight w-full">{{ $p->name }}</div>
                    <div class="text-rose-600 text-base font-bold mb-2">{{ number_format($p->price, 0, ',', '.') }} VND</div>
                </a>
                <div class="flex flex-col gap-1 w-full mt-1">
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
            @endforeach
        </div>
    </section>

    {{-- Phần bình luận đánh giá --}}
    <section class="mt-12 bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Đánh giá sản phẩm</h2>
        
        {{-- Form viết đánh giá --}}
        <div class="mb-8 border-b border-gray-200 pb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Viết đánh giá của bạn</h3>
            <form id="review-form" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                {{-- Đánh giá sao --}}
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Đánh giá:</span>
                    <div class="flex space-x-1" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition-colors duration-200" data-rating="{{ $i }}">★</button>
                        @endfor
                    </div>
                    <span id="rating-text" class="text-sm text-gray-600 ml-2"></span>
                </div>
                <input type="hidden" name="rating" id="rating-input" value="0">
                
                {{-- Tên người đánh giá --}}
                @auth
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên của bạn</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md border">
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('images/avatars/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-rose-100 flex items-center justify-center">
                                    <span class="text-rose-600 font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
                    </div>
                    <input type="hidden" id="reviewer-name" name="reviewer_name" value="{{ Auth::user()->name }}">
                </div>
                @else
                <div>
                    <label for="reviewer-name" class="block text-sm font-medium text-gray-700 mb-1">Tên của bạn</label>
                    <input type="text" id="reviewer-name" name="reviewer_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent" placeholder="Nhập tên của bạn" required>
                </div>
                @endauth
                
                {{-- Nội dung bình luận --}}
                <div>
                    <label for="review-content" class="block text-sm font-medium text-gray-700 mb-1">Nội dung đánh giá</label>
                    <textarea id="review-content" name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                </div>
                
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-md font-semibold hover:bg-rose-600 transition-colors duration-200">Gửi đánh giá</button>
            </form>
        </div>
        
        {{-- Danh sách đánh giá --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Đánh giá từ khách hàng</h3>
            <div id="reviews-container">
                @forelse($reviews as $review)
                <div class="review-item border-b border-gray-100 pb-4 mb-4" data-review-id="{{ $review->id }}"
                     @auth @if($review->user_id === Auth::id()) data-user-review="true" @endif @endauth>
                    <div class="flex items-center justify-between mb-2">
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
                                <p class="font-semibold text-gray-800">{{ $review->reviewer_name }}</p>
                                <div class="flex items-center space-x-2">
                                    <div class="flex text-yellow-400">
                                        <span class="review-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @auth
                        @if($review->user_id === Auth::id())
                        <div class="flex space-x-2">
                            <button class="delete-review-btn text-red-600 hover:text-red-800 text-sm font-medium"
                                    data-review-id="{{ $review->id }}">
                                Xóa
                            </button>
                        </div>
                        @endif
                        @endauth
                    </div>
                    <p class="text-gray-700 ml-13 review-content">{{ $review->content }}</p>
                    <div class="review-rating hidden">{{ $review->rating }}</div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                    <p class="text-sm">Hãy là người đầu tiên đánh giá sản phẩm!</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<!-- Thông báo Toast -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 cursor-pointer hidden">
    <div class="flex items-center justify-between">
        <span id="toast-message"></span>
        <button id="toast-close" class="ml-4 text-white hover:text-gray-200 font-bold text-lg">&times;</button>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div id="confirm-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-rose-100 rounded-full">
                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Xác nhận xóa đánh giá</h3>
            <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn xóa đánh giá này? Hành động này không thể hoàn tác.</p>
            <div class="flex space-x-3">
                <button id="confirm-cancel" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Hủy
                </button>
                <button id="confirm-delete" class="flex-1 px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-colors duration-200">
                    Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý tăng/giảm số lượng
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const cartQuantityInput = document.getElementById('cart-quantity');
    const buyQuantityInput = document.getElementById('buy-quantity');
    
    // Cập nhật hidden inputs khi thay đổi số lượng
    function updateQuantity() {
        const qty = quantityInput.value;
        cartQuantityInput.value = qty;
        buyQuantityInput.value = qty;
    }
    
    decreaseBtn.addEventListener('click', function() {
        if (quantityInput.value > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
            updateQuantity();
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        if (quantityInput.value < 99) {
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateQuantity();
        }
    });
    
    quantityInput.addEventListener('input', function() {
        updateQuantity();
    });
    
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
    
    // Xử lý đánh giá sao
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    let currentRating = 0;
    
    const ratingTexts = {
        1: 'Rất tệ',
        2: 'Tệ',
        3: 'Bình thường',
        4: 'Tốt',
        5: 'Rất tốt'
    };
    
    starButtons.forEach(button => {
        button.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
        
        button.addEventListener('mouseout', function() {
            highlightStars(currentRating);
        });
        
        button.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.rating);
            ratingInput.value = currentRating;
            ratingText.textContent = ratingTexts[currentRating];
            highlightStars(currentRating);
        });
    });
    
    function highlightStars(rating) {
        starButtons.forEach((button, index) => {
            if (index < rating) {
                button.classList.remove('text-gray-300');
                button.classList.add('text-yellow-400');
            } else {
                button.classList.remove('text-yellow-400');
                button.classList.add('text-gray-300');
            }
        });
    }
    
    // Xử lý form gửi đánh giá
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = ratingInput.value;
            const reviewerName = document.getElementById('reviewer-name').value;
            const content = document.getElementById('review-content').value;
            
            if (rating == 0) {
                showToast('Vui lòng chọn số sao đánh giá!');
                return;
            }
            
            if (!content.trim()) {
                showToast('Vui lòng nhập nội dung đánh giá!');
                return;
            }
            
            if (!reviewerName.trim()) {
                showToast('Vui lòng đăng nhập để đánh giá!');
                return;
            }
            
            // Gửi AJAX request đến server
            const formData = new FormData();
            formData.append('rating', rating);
            formData.append('content', content);
            formData.append('reviewer_name', reviewerName);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('{{ route("reviews.store", $product->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tạo đánh giá mới và thêm vào đầu danh sách
                    const newReview = createReviewElement(reviewerName, rating, content, true);
                    const reviewsContainer = document.getElementById('reviews-container');
                    
                    // Xóa thông báo "chưa có đánh giá" nếu có
                    const emptyMessage = reviewsContainer.querySelector('.text-center.py-8');
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }
                    
                    reviewsContainer.insertAdjacentHTML('afterbegin', newReview);
                    
                    // Cập nhật điểm đánh giá trung bình
                    updateAverageRating();
                    
                    // Reset form
                    reviewForm.reset();
                    currentRating = 0;
                    ratingInput.value = 0;
                    ratingText.textContent = '';
                    highlightStars(0);
                    
                    showToast(data.message);
                } else {
                    showToast(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại!');
            });
        });
    }
    
    function createReviewElement(name, rating, content, isCurrentUser = false) {
        const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        const firstLetter = name.charAt(0).toUpperCase();
        
        if (!isCurrentUser) {
            isCurrentUser = name === '{{ Auth::user()->name ?? "" }}';
        }
        
        // Avatar cho user hiện tại
        const currentUserAvatar = '{{ Auth::user()->avatar ?? "" }}';
        const avatarHtml = (isCurrentUser && currentUserAvatar)
            ? `<img src="{{ asset('images/avatars/') }}/${currentUserAvatar}" alt="Avatar" class="w-full h-full object-cover">`
            : `<div class="w-full h-full bg-rose-100 flex items-center justify-center">
                 <span class="text-rose-600 font-semibold">${firstLetter}</span>
               </div>`;
        
        const editDeleteButtons = isCurrentUser ? `
            <div class="flex space-x-2">
                <button class="delete-review-btn text-red-600 hover:text-red-800 text-sm font-medium" data-review-id="new-${Date.now()}">Xóa</button>
            </div>
        ` : '';
        
        return `
            <div class="review-item border-b border-gray-100 pb-4 mb-4" data-review-id="new-${Date.now()}" ${isCurrentUser ? 'data-user-review="true"' : ''}>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200">
                            ${avatarHtml}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">${name}</p>
                            <div class="flex items-center space-x-2">
                                <div class="flex text-yellow-400">
                                    <span class="review-stars">${stars}</span>
                                </div>
                                <span class="text-sm text-gray-500">Vừa xong</span>
                            </div>
                        </div>
                    </div>
                    ${editDeleteButtons}
                </div>
                <p class="text-gray-700 ml-13 review-content">${content}</p>
                <div class="review-rating hidden">${rating}</div>
            </div>
        `;
    }
    
    // Xử lý xóa đánh giá
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-review-btn')) {
            const reviewId = e.target.dataset.reviewId;
            const reviewItem = document.querySelector(`[data-review-id="${reviewId}"]`);
            deleteReview(reviewItem);
        }
    });
    
    
    function deleteReview(reviewItem) {
        const modal = document.getElementById('confirm-modal');
        const confirmBtn = document.getElementById('confirm-delete');
        const cancelBtn = document.getElementById('confirm-cancel');
        
        // Hiển thị modal
        modal.classList.remove('hidden');
        
        // Xử lý khi nhấn Hủy
        const handleCancel = () => {
            modal.classList.add('hidden');
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
        };
        
        // Xử lý khi nhấn Xóa
        const handleConfirm = () => {
            modal.classList.add('hidden');
            
            const reviewId = reviewItem.dataset.reviewId;
            
            // Nếu là review mới tạo (chưa có trong database)
            if (reviewId.startsWith('new-')) {
                reviewItem.style.transition = 'opacity 0.3s ease-in-out';
                reviewItem.style.opacity = '0';
                setTimeout(() => {
                    reviewItem.remove();
                    // Cập nhật điểm đánh giá trung bình
                    updateAverageRating();
                    showToast('Đánh giá đã được xóa!');
                }, 300);
                return;
            }
            
            // Gửi AJAX request để xóa review trong database
            fetch(`${window.location.origin}/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    reviewItem.style.transition = 'opacity 0.3s ease-in-out';
                    reviewItem.style.opacity = '0';
                    setTimeout(() => {
                        reviewItem.remove();
                        // Cập nhật điểm đánh giá trung bình
                        updateAverageRating();
                        showToast(data.message);
                    }, 300);
                } else {
                    showToast(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại!');
            });
            
            // Cleanup listeners
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
        };
        
        // Thêm event listeners
        confirmBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);
    }
    
    // Function để cập nhật điểm đánh giá trung bình
    function updateAverageRating() {
        const reviewItems = document.querySelectorAll('.review-item');
        const totalReviews = reviewItems.length;
        
        if (totalReviews === 0) {
            // Hiển thị "Chưa có đánh giá"
            const ratingContainer = document.querySelector('.flex.items-center.space-x-2.mb-6');
            if (ratingContainer) {
                ratingContainer.innerHTML = `
                    <div class="flex text-gray-300 text-lg">
                        <span>★★★★★</span>
                    </div>
                    <span class="text-gray-500">Chưa có đánh giá</span>
                `;
            }
            return;
        }
        
        // Tính toán điểm trung bình
        let totalRating = 0;
        reviewItems.forEach(item => {
            const ratingElement = item.querySelector('.review-rating');
            if (ratingElement) {
                totalRating += parseInt(ratingElement.textContent);
            }
        });
        
        const averageRating = Math.round((totalRating / totalReviews) * 10) / 10;
        
        // Cập nhật hiển thị
        const ratingContainer = document.querySelector('.flex.items-center.space-x-2.mb-6');
        if (ratingContainer) {
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(averageRating)) {
                    starsHtml += '<span>★</span>';
                } else if (i === Math.ceil(averageRating) && averageRating - Math.floor(averageRating) >= 0.5) {
                    starsHtml += '<span>★</span>';
                } else {
                    starsHtml += '<span class="text-gray-300">★</span>';
                }
            }
            
            ratingContainer.innerHTML = `
                <div class="flex text-yellow-400 text-lg">
                    ${starsHtml}
                </div>
                <span class="text-lg font-semibold text-gray-800">${averageRating}/5</span>
                <span class="text-gray-600">(${totalReviews} đánh giá)</span>
            `;
        }
    }
});
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý xóa đánh giá với SweetAlert
    document.querySelectorAll('.delete-review-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            
            Swal.fire({
                title: 'Xác nhận xóa đánh giá',
                text: "Bạn có chắc chắn muốn xóa đánh giá này? Hành động này không thể hoàn tác.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`${window.location.origin}/reviews/${reviewId}`)
                        .then(response => {
                            if (response.data.success) {
                                document.querySelector(`.review-item[data-review-id="${reviewId}"]`).remove();
                                Swal.fire(
                                    'Đã xóa!',
                                    'Đánh giá của bạn đã được xóa thành công.',
                                    'success'
                                );
                            }
                        })
                        .catch(error => {
                            Swal.fire(
                                'Lỗi!',
                                'Đã có lỗi xảy ra khi xóa đánh giá.',
                                'error'
                            );
                        });
                }
            });
        });
    });
});
</script>
@endpush
