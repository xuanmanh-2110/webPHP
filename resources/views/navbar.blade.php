<nav class="bg-white flex justify-between items-center px-5 py-4 border-b border-gray-200 mb-4 w-full rounded-xl">
    <div class="container mx-auto flex justify-between items-center">
        <a class="text-gray-800 text-lg font-semibold" href="http://127.0.0.1:8000">FlowerShop</a>
        <button class="block lg:hidden" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="h-6 w-6 text-gray-500"></span>
        </button>
        <div class="hidden lg:flex lg:items-center lg:w-auto flex-grow" id="navbarNav">
            <ul class="flex flex-col lg:flex-row lg:ml-auto">
                <li class="lg:ml-4">
                    <form action="http://127.0.0.1:8000/logout" method="POST" class="inline">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">
                        <button type="submit" class="text-red-600 hover:text-red-800 px-0 py-0">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>