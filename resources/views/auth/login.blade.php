@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-6 md:p-8 bg-white rounded-2xl shadow-lg border border-gray-300">
        <div class="py-8 md:py-10 px-4 md:px-6">
            <h3 class="text-center mb-6 text-2xl font-bold text-rose-600">Đăng nhập</h3>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="mb-0 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Email hoặc Tên đăng nhập</label>
                    <input type="text" name="login" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" value="{{ old('login') }}" required autofocus autocomplete="off">
                </div>
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Mật khẩu</label>
                    <input type="password" name="password" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" required>
                </div>
                <div class="flex items-center mb-5">
                    <input class="form-checkbox h-4 w-4 text-rose-500 transition duration-150 ease-in-out rounded border-gray-300 focus:ring-rose-500" type="checkbox" name="remember" id="remember">
                    <label class="ml-2 block text-gray-900" for="remember">Ghi nhớ tôi</label>
                </div>
                <button type="submit" class="w-full bg-rose-500 text-white font-semibold py-3 px-6 rounded-xl shadow-md hover:bg-rose-600 transition-all duration-300">Đăng nhập</button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('register') }}" class="text-rose-500 hover:text-rose-600 text-sm">Chưa có tài khoản? Đăng ký</a>
            </div>
        </div>
    </div>
</div>
@endsection
