@extends('layouts.app')
<header class="bg-rose-500 text-white text-center py-2 text-xs md:text-sm">Đang mở cửa giao hoa - Phí giao hàng toàn TP chỉ 12.950đ</header>
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
            <span class="text-sm font-bold whitespace-normal md:whitespace-nowrap">Xin chào, {{ Auth::user()->name }}</span>
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
  <section class="bg-pink-100 p-[5%] flex flex-col md:flex-row items-center justify-between gap-5 md:gap-8 overflow-hidden">
    <div class="flex-1 max-w-full md:max-w-[50%]">
      <p><strong>Hoa đào khoe sắc</strong></p>
      <h1 class="text-[clamp(24px,5vw,36px)] mb-2.5">Gửi trọn yêu thương đến người bạn quý</h1>
      <button id="order-now-btn" class="px-5 py-2.5 border-none bg-rose-500 text-white cursor-pointer rounded-md text-[clamp(16px,2.5vw,18px)] font-semibold shadow-md shadow-rose-200 transition-all duration-200 ease-in-out outline-none active:scale-96 active:shadow-sm hover:bg-rose-600 hover:shadow-lg hover:scale-104">Đặt hoa ngay</button>
    </div>
    
    <div id="hero-slideshow" class="relative w-full md:max-w-[550px] h-auto aspect-video">
      @for($i = 0; $i < 5; $i++)
        @php
          $img = isset($products[$i]) && $products[$i]->image
            ? asset('images/products/' . $products[$i]->image)
            : 'https://via.placeholder.com/550x300?text=Hoa+tươi';
          $alt = isset($products[$i]) ? $products[$i]->name : 'Hoa tươi';
        @endphp
        <img
          src="{{ $img }}"
          alt="{{ $alt }}"
          class="w-full h-full object-cover rounded-md absolute top-0 left-0 transition-transform duration-700 ease-[cubic-bezier(.7,0,.3,1)]"
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
          <div class="bg-white rounded-xl shadow-md p-4 text-center transition-all duration-300 ease-in-out relative overflow-hidden border border-gray-200 flex flex-col items-center h-full hover:translate-y-[-5px] hover:shadow-lg">
            @if($p->image)
              <img src="{{ asset('images/products/' . $p->image) }}" alt="{{ $p->name }}" class="w-full max-w-[250px] h-auto aspect-square object-cover rounded-md mx-auto transition-transform duration-300 group-hover:scale-105">
            @else
              <img src="https://via.placeholder.com/250x250" alt="No Image" class="w-full max-w-[250px] h-auto aspect-square object-cover rounded-md mx-auto transition-transform duration-300 group-hover:scale-105">
            @endif
            <p>{{ $p->name }}<br><strong class="text-rose-600">{{ number_format($p->price, 0, ',', '.') }} VND</strong></p>
          </div>
        </a>
      @endforeach
    </div>
  </section>

  <section class="bg-pink-50 p-[5%] text-center">
    <h2 class="text-rose-600 text-[clamp(20px,4vw,26px)]">Khuyến mãi HOT! Giảm giá đến <span class="text-rose-500">20%</span></h2>
    <p>Giảm giá xả kho lên đến 20%</p>
    <button id="order-now-btn-promo" class="px-5 py-2.5 border-none bg-rose-500 text-white cursor-pointer rounded-md text-[clamp(16px,2.5vw,18px)] font-semibold shadow-md shadow-rose-200 transition-all duration-200 ease-in-out outline-none active:scale-96 active:shadow-sm hover:bg-rose-600 hover:shadow-lg hover:scale-104">Đặt hoa ngay</button>
  </section>
      <footer class="bg-gray-200 px-4 py-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 text-[clamp(12px,2.5vw,14px)]">
        <div class="min-w-full sm:min-w-[150px]">
          <h4>Tài khoản</h4>
          <p>123 Đường Ví Dụ, TP. Hồ Chí Minh</p>
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
        © 2025 Thiết kế bởi Shawon - Shop Hoa Tươi
      </div>
@endsection

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const manageButton = document.getElementById('manage-button');
    const manageDropdown = document.getElementById('manage-dropdown');
    const manageContainer = manageButton.closest('.relative'); // Lấy div cha có class relative

    if (manageContainer && manageDropdown) {
      let timeoutId;

      manageContainer.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId); // Xóa timeout nếu có
        manageDropdown.classList.remove('hidden');
        manageDropdown.classList.add('block');
      });

      manageContainer.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(function() {
          manageDropdown.classList.add('hidden');
          manageDropdown.classList.remove('block');
        }, 300); // 300ms delay
      });

      manageDropdown.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId); // Xóa timeout nếu chuột di vào dropdown
      });

      manageDropdown.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(function() {
          manageDropdown.classList.add('hidden');
          manageDropdown.classList.remove('block');
        }, 300); // 300ms delay
      });
    }
  });
</script>