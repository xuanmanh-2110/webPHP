@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <a href="{{ url('/') }}" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4">&larr; Quay về trang chính</a>
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Sửa sản phẩm</h2>
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        @csrf @method('PUT')
        
        <!-- Hiển thị ảnh hiện tại -->
        @if($product->image)
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Ảnh hiện tại:</label>
            <img src="{{ asset('images/products/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-md border border-gray-300 shadow-sm">
        </div>
        @endif
        
        <!-- Upload ảnh mới -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-medium mb-1">Ảnh mới (tùy chọn):</label>
            <input type="file" name="image" id="image" accept="image/*" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">
            <p class="text-gray-500 text-xs mt-1">Chọn ảnh mới nếu muốn thay đổi. Để trống nếu giữ nguyên ảnh cũ.</p>
            @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-medium mb-1">Tên:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 text-sm font-medium mb-1">Giá:</label>
            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
            @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-medium mb-1">Mô tả:</label>
            <textarea name="description" id="description" class="form-textarea mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-rose-500 text-white hover:bg-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200">Lưu</button>
    </form>
</div>
@endsection
