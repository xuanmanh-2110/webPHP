<nav class="bg-white flex justify-between items-center px-5 py-4 border-b border-gray-200 mb-4 w-full rounded-xl">
    <div class="container mx-auto flex justify-between items-center">
        <a class="text-gray-800 text-lg font-semibold" href="http://127.0.0.1:8000">FlowerShop</a>
        <button class="block lg:hidden" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="h-6 w-6 text-gray-500"></span>
        </button>
        <div class="hidden lg:flex lg:items-center lg:w-auto flex-grow" id="navbarNav">
            <ul class="flex flex-col lg:flex-row lg:ml-auto items-center">
                @auth
                <li class="lg:ml-4">
                    <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 flex items-center">
                        <span class="mr-1">👤</span>
                        <span class="text-sm font-bold whitespace-normal md:whitespace-nowrap">Xin chào, {{ Auth::user()->name }}</span>
                    </a>
                </li>
                <li class="lg:ml-4">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 px-3 py-2">Đăng xuất</button>
                    </form>
                </li>
                @else
                <li class="lg:ml-4">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 px-3 py-2">Đăng nhập</a>
                </li>
                <li class="lg:ml-2">
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded">Đăng ký</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>