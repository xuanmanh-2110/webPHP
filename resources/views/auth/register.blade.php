@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-6 md:p-8 bg-white rounded-2xl shadow-lg border border-gray-300">
        <div class="py-8 md:py-10 px-4 md:px-6">
            <h3 class="text-center mb-6 text-2xl font-bold text-rose-600">Đăng ký tài khoản</h3>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('success') }}</div>
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

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Họ tên</label>
                    <input type="text" name="name" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" value="{{ old('name') }}" required autocomplete="off">
                </div>
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" value="{{ old('email') }}" required autocomplete="off">
                </div>
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Mật khẩu</label>
                    <input type="password" name="password" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" required>
                </div>
                <div class="mb-5">
                    <label class="block text-gray-800 text-sm font-medium mb-1">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="border-2 border-gray-300 rounded-lg p-2 w-full text-base focus:border-rose-500 focus:ring-rose-500 focus:outline-none transition-colors duration-300" required>
                </div>
                <button type="submit" class="w-full bg-rose-500 text-white font-semibold py-3 px-6 rounded-xl shadow-md hover:bg-rose-600 transition-all duration-300">Đăng ký</button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-rose-500 hover:text-rose-600 text-sm">Đã có tài khoản? Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
@endsection
