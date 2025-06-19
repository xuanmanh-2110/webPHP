@extends('layouts.app')
<header class="bg-rose-500 text-white text-center py-2 text-xs md:text-sm">Đang mở cửa giao hoa - Phí giao hàng chỉ 0đ</header>
<nav class="bg-white flex justify-between items-center px-4 py-3 border-b border-gray-200">
    <div class="flex items-center">
      <div class="text-gray-800 font-bold ml-0 md:ml-25">Shop Hoa Tươi</div>
                  @auth
                    @if(auth()->user()->is_admin)
                      <div class="flex items-center">
                        <div class="relative">
                            <button id="manage-button" class="border-none px-4 py-2 font-bold text-gray-800 hover:text-red-600 whitespace-normal md:whitespace-nowrap">
                                Quản lý
                            </button>
                            <div id="manage-dropdown" class="hidden absolute top-[110%] left-0 bg-white border-none rounded-xl shadow-lg min-w-[200px] z-50">
                                <a class="block px-5 py-3 border-b-0 bg-white hover:bg-red-100 hover:text-red-700" href="{{ route('products.index') }}">Quản lý sản phẩm</a>
                                <a class="block px-5 py-3 border-b-0 bg-white hover:bg-red-100 hover:text-red-700" href="{{ route('orders.index') }}">Quản lý đơn hàng</a>
                                <a class="block px-5 py-3 bg-white hover:bg-red-100 hover:text-red-700" href="{{ route('customers.index') }}">Quản lý khách hàng</a>
                            </div>
                        </div>
                      </div>
                    @endif
                  @endauth
    </div>
    <div class="flex items-center flex-wrap justify-center md:flex-nowrap md:justify-end w-full md:max-w-3xl gap-x-4 mr-0 md:mr-25">
        <form id="search-form" autocomplete="off" class="relative w-full md:max-w-[250px] flex items-center">
            <input type="text" id="search-input" name="q" placeholder="Tìm sản phẩm..." class="px-3 py-1 border border-gray-300 rounded-md w-full text-xs">
            <div id="search-suggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md z-50 hidden"></div>
        </form>
        @auth
            <a href="#" class="no-underline text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Trang chủ</a>
            <a href="{{ route('products.shop') }}" class="no-underline text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Cửa hàng</a>
            <a href="{{ route('orders.history') }}" class="no-underline text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Lịch sử mua</a>
            <a href="{{ route('cart.index') }}" class="text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Giỏ hàng</a>
            <a href="{{ route('profile.show') }}" class="text-sm font-bold whitespace-normal md:whitespace-nowrap text-gray-800 hover:text-red-600 no-underline">Xin chào, {{ Auth::user()->name }}</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-transparent text-red-700 border border-red-700 px-2 py-1 rounded-md text-sm font-bold cursor-pointer whitespace-normal md:whitespace-nowrap hover:bg-red-700 hover:text-white">Đăng xuất</button>
            </form>
        @else
            <div class="flex items-center gap-x-4">
                <a href="{{ route('login') }}" class="no-underline text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Đăng nhập</a>
                <a href="{{ route('register') }}" class="no-underline text-gray-800 font-bold text-sm whitespace-normal md:whitespace-nowrap hover:text-red-600">Đăng ký</a>
            </div>
        @endauth
    </div>
  </nav>
@section('content')
  <section class="relative bg-pink-100 p-[5%] flex flex-col md:flex-row items-center justify-between gap-5 md:gap-8 overflow-hidden">
    <!-- Static background blur effect -->
    <div class="absolute inset-0 z-0">
      @if(isset($products[0]) && $products[0]->image)
        <img
          src="{{ asset('images/products/' . $products[0]->image) }}"
          alt="Background"
          class="w-full h-full object-cover blur-sm scale-105">
      @else
        <img
          src="https://via.placeholder.com/800x600/FFB6C1/FFFFFF?text=Background"
          alt="Background"
          class="w-full h-full object-cover blur-sm scale-105">
      @endif
      <div class="absolute inset-0 bg-pink-100/40"></div>
    </div>

    <!-- Content overlay -->
    <div class="relative z-10 flex-1 max-w-full md:max-w-[50%]">
      <p><strong>Gửi hoa</strong></p>
      <h1 class="text-[clamp(24px,5vw,36px)] mb-2.5">Thay lời trái tim muốn nói.</h1>
      <button id="order-now-btn" class="px-5 py-2.5 border-none bg-rose-500 text-white cursor-pointer rounded-md text-[clamp(16px,2.5vw,18px)] font-semibold shadow-md shadow-rose-200 transition-all duration-200 ease-in-out outline-none active:scale-96 active:shadow-sm hover:bg-rose-600 hover:shadow-lg hover:scale-104">Đặt hoa ngay</button>
    </div>
    
    <div id="hero-slideshow" class="relative z-10 w-full md:max-w-[400px] h-auto aspect-square">
      @for($i = 0; $i < 5; $i++)
        @php
          $img = isset($products[$i]) && $products[$i]->image
            ? asset('images/products/' . $products[$i]->image)
            : 'https://via.placeholder.com/400x400?text=Hoa+tươi';
          $alt = isset($products[$i]) ? $products[$i]->name : 'Hoa tươi';
        @endphp
        <img
          src="{{ $img }}"
          alt="{{ $alt }}"
          class="w-full h-full object-cover rounded-md absolute top-0 left-0 transition-transform duration-700 ease-[cubic-bezier(.7,0,.3,1)] shadow-lg"
          style="opacity:{{ $i === 0 ? '1' : '0' }}; transform: translateX({{ $i === 0 ? '0' : '100%' }}); z-index:{{ 10 - $i }};">
        @endfor
      </div>
    </section>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const slideshow = document.getElementById('hero-slideshow');
        const images = slideshow.querySelectorAll('img');
        let currentSlide = 0;

        function showSlide(index) {
          images.forEach((img, i) => {
            img.style.transition = 'opacity 1s ease-in-out, transform 1s ease-in-out';
            if (i === index) {
              img.style.opacity = '1';
              img.style.transform = 'translateX(0)';
            } else {
              img.style.opacity = '0';
              img.style.transform = 'translateX(100%)'; // Đẩy ảnh ra ngoài bên phải
            }
          });
        }

        function nextSlide() {
          // Ẩn ảnh hiện tại bằng cách dịch chuyển sang phải
          images[currentSlide].style.opacity = '0';
          images[currentSlide].style.transform = 'translateX(100%)';

          currentSlide = (currentSlide + 1) % images.length;

          // Đặt ảnh tiếp theo ở vị trí ban đầu bên trái trước khi hiển thị
          images[currentSlide].style.transition = 'none'; // Tắt transition tạm thời
          images[currentSlide].style.transform = 'translateX(-100%)';
          images[currentSlide].style.opacity = '0'; // Đảm bảo ảnh ẩn trước khi dịch chuyển

          // Buộc trình duyệt render lại để transition hoạt động
          void images[currentSlide].offsetWidth;

          // Hiển thị ảnh tiếp theo bằng cách dịch chuyển vào
          images[currentSlide].style.transition = 'opacity 1s ease-in-out, transform 1s ease-in-out';
          images[currentSlide].style.opacity = '1';
          images[currentSlide].style.transform = 'translateX(0)';
        }

        // Hiển thị ảnh đầu tiên khi tải trang
        showSlide(currentSlide);

        // Tự động chuyển đổi ảnh sau mỗi 3 giây
        setInterval(nextSlide, 3000);
      });
    </script>

  <section class="p-[5%]">
    <h2 class="text-center mb-5 text-[clamp(20px,4vw,28px)] text-rose-600">Sản phẩm mới nhất</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 justify-center">
      @foreach($products as $idx => $p)
        <a href="{{ route('products.show', $p->id) }}" class="no-underline text-inherit group">
          <div class="bg-white rounded-xl shadow-md py-6 px-2 text-center transition-all duration-300 ease-in-out relative overflow-hidden border border-gray-200 flex flex-col h-full hover:translate-y-[-5px] hover:shadow-lg">
            <div class="flex justify-center items-center mb-6 overflow-hidden rounded-md mx-2">
              @if($p->image)
                <img src="{{ asset('images/products/' . $p->image) }}" alt="{{ $p->name }}" class="w-full h-[240px] object-cover transition-transform duration-300 group-hover:scale-105">
              @else
                <img src="https://via.placeholder.com/240x240" alt="No Image" class="w-full h-[240px] object-cover transition-transform duration-300 group-hover:scale-105">
              @endif
            </div>
            <p class="text-lg font-bold text-rose-600 px-2 pb-2">{{ $p->name }}</p>
          </div>
        </a>
      @endforeach
    </div>
  </section>

  @if($latestProducts->count() > 0)
  <section class="bg-pink-50 p-[5%]">
    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
      <!-- Chữ bên trái -->
      <div class="flex-1 text-left">
        <h2 class="text-rose-600 text-[clamp(20px,4vw,26px)] mb-4">Khuyến mãi HOT! Giảm giá đến <span class="text-rose-500">20%</span></h2>
        <p class="mb-6 text-lg">Sản phẩm mới nhất - Giảm giá xả kho lên đến 20%</p>
        <p class="text-gray-600 mb-6">Đừng bỏ lỡ cơ hội sở hữu sản phẩm mới nhất với mức giá ưu đãi nhất!</p>
      </div>
      
      <!-- Carousel sản phẩm bên phải -->
      <div class="flex-1 max-w-4xl">
        <div class="relative overflow-hidden" style="width: 100%; height: 400px;">
          <div id="carousel-container" class="flex transition-transform duration-1000 ease-in-out" style="gap: 20px; width: {{ $latestProducts->count() * 3 * 280 }}px;">
            @foreach(range(0, 2) as $clone)
            @foreach($latestProducts as $index => $product)
            <div class="carousel-item flex-shrink-0 transition-all duration-1000 ease-in-out"
                 data-index="{{ $index }}"
                 data-clone="{{ $clone }}"
                 data-product-id="{{ $product->id }}"
                 data-product-name="{{ $product->name }}"
                 data-product-price="{{ $product->price }}"
                 style="width: 260px; display: flex; align-items: center; justify-content: center;">
              <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 text-center transition-all duration-1000 ease-in-out">
                <a href="{{ route('products.show', $product->id) }}" class="block no-underline text-inherit hover:opacity-80 transition-opacity duration-200">
                  <img src="{{ $product->image ? asset('images/products/' . $product->image) : 'https://via.placeholder.com/200x200' }}"
                       alt="{{ $product->name }}"
                       class="w-[200px] h-[200px] object-cover rounded-md border border-gray-300 mb-3 mx-auto transition-transform duration-1000 ease-in-out">
                  
                  <h3 class="text-base font-semibold mb-2 text-rose-600 min-h-[3rem] flex items-center justify-center leading-tight transition-all duration-1000 ease-in-out">{{ $product->name }}</h3>
                  
                  <div class="mb-3 transition-all duration-1000 ease-in-out">
                    <span class="text-sm text-gray-500 line-through mr-2">{{ number_format($product->price, 0, ',', '.') }} VND</span>
                    <br>
                    <span class="text-rose-600 text-lg font-bold">{{ number_format($product->price * 0.8, 0, ',', '.') }} VND</span>
                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs ml-2">-20%</span>
                  </div>
                </a>
              </div>
            </div>
            @endforeach
            @endforeach
          </div>
        </div>
        
        <!-- Indicators -->
        <div class="flex justify-center mt-12 space-x-4">
          @foreach($latestProducts as $index => $product)
          <button class="carousel-indicator w-4 h-4 rounded-full transition-all duration-200"
                  data-index="{{ $index }}"
                  style="background-color: {{ $index === 0 ? '#f43f5e' : '#d1d5db' }}"></button>
          @endforeach
        </div>
      </div>
    </div>
  </section>
  @endif
      <footer class="bg-gray-200 px-4 py-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 text-[clamp(12px,2.5vw,14px)]">
        <div class="min-w-full sm:min-w-[150px]">
          <h4>Tài khoản</h4>
          <p>Yên Nghĩa, Hà Đông, TP. Hà Nội</p>
        </div>
        <div class="min-w-full sm:min-w-[150px]">
          <h4>Danh mục</h4>
          <p>Shopify, Magento, Opencart</p>
        </div>
        <div class="min-w-full sm:min-w-[150px]">
          <h4>Thông tin</h4>
          <p>Về chúng tôi, Liên hệ, Giao hàng</p>
        </div>
        <div class="min-w-full sm:min-w-[150px]">
          <h4>Liên kết nhanh</h4>
          <p>Tra cứu đơn hàng, Hướng dẫn mua</p>
        </div>
      </footer>

      <div class="bg-rose-500 text-white text-center py-2.5 text-[clamp(10px,2vw,12px)]">
        © 2025 Thiết kế bởi XManh - Shop Hoa Tươi
      </div>
@endsection

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Manage dropdown
    const manageButton = document.getElementById('manage-button');
    const manageDropdown = document.getElementById('manage-dropdown');
    const manageContainer = manageButton ? manageButton.closest('.relative') : null;

    if (manageContainer && manageDropdown) {
      let timeoutId;

      manageContainer.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId);
        manageDropdown.classList.remove('hidden');
        manageDropdown.classList.add('block');
      });

      manageContainer.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(function() {
          manageDropdown.classList.add('hidden');
          manageDropdown.classList.remove('block');
        }, 300);
      });

      manageDropdown.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId);
      });

      manageDropdown.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(function() {
          manageDropdown.classList.add('hidden');
          manageDropdown.classList.remove('block');
        }, 300);
      });
    }

    // Infinite scrolling carousel with scale effects
    const carouselContainer = document.getElementById('carousel-container');
    const carouselItems = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const totalItems = {{ $latestProducts->count() }};
    let currentIndex = totalItems; // Start from middle set
    let isTransitioning = false;
    
    if (carouselContainer && carouselItems.length > 0) {
      // Set initial position (centered)
      const containerWidth = carouselContainer.parentElement.offsetWidth;
      const itemOffset = (containerWidth / 2) - (260 / 2);
      const initialTranslateX = -(currentIndex * 280) + itemOffset;
      carouselContainer.style.transform = `translateX(${initialTranslateX}px)`;
      
      function updateCarousel(smooth = true) {
        if (smooth) {
          carouselContainer.style.transition = 'transform 1000ms ease-in-out';
        } else {
          carouselContainer.style.transition = 'none';
        }
        
        // Center the current item (offset by half container width minus half item width)
        const containerWidth = carouselContainer.parentElement.offsetWidth;
        const itemOffset = (containerWidth / 2) - (260 / 2); // 260px is item width
        const translateX = -(currentIndex * 280) + itemOffset;
        carouselContainer.style.transform = `translateX(${translateX}px)`;
        
        // Update scale effects for all items
        carouselItems.forEach((item, index) => {
          const card = item.querySelector('div');
          const title = card.querySelector('h3');
          const price = card.querySelector('div:last-child');
          
          if (index === currentIndex) {
            // Center item - active (scale up but not too much)
            card.style.transform = 'scale(1.05)';
            card.style.opacity = '1';
            card.style.filter = 'grayscale(0)';
            title.classList.remove('text-sm');
            title.classList.add('text-xl');
            price.classList.remove('text-xs');
            price.classList.add('text-base');
          } else {
            // Other items (scale down)
            card.style.transform = 'scale(0.9)';
            card.style.opacity = '0.7';
            card.style.filter = 'grayscale(100%)';
            title.classList.remove('text-xl');
            title.classList.add('text-sm');
            price.classList.remove('text-base');
            price.classList.add('text-xs');
          }
        });

        // Update indicators based on actual product index
        const indicatorIndex = currentIndex % totalItems;
        indicators.forEach((indicator, index) => {
          indicator.style.backgroundColor = index === indicatorIndex ? '#f43f5e' : '#d1d5db';
        });
      }

      function nextSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex++;
        updateCarousel();
        
        setTimeout(() => {
          if (currentIndex >= totalItems * 2) {
            currentIndex = totalItems;
            updateCarousel(false);
          }
          isTransitioning = false;
        }, 1000);
      }

      function prevSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex--;
        updateCarousel();
        
        setTimeout(() => {
          if (currentIndex <= 0) {
            currentIndex = totalItems;
            updateCarousel(false);
          }
          isTransitioning = false;
        }, 1000);
      }

      function goToSlide(index) {
        if (isTransitioning) return;
        currentIndex = totalItems + index;
        updateCarousel();
      }

      // Event listeners
      indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => goToSlide(index));
      });

      // Auto-play every 5 seconds
      setInterval(nextSlide, 5000);
      updateCarousel();
    }
  });
</script>