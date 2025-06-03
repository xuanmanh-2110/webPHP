<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

use App\Models\Product;
Route::get('/', function () {
    $products = Product::latest()->take(10)->get();
    return view('welcome', compact('products'));
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return 'Chào mừng bạn đến Dashboard!';
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/suggest', [App\Http\Controllers\ProductController::class, 'suggest'])->name('products.suggest');
    Route::get('/shop', [App\Http\Controllers\ProductController::class, 'shop'])->name('products.shop');
});

Route::resource('customers', CustomerController::class);
Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');

    // Lịch sử mua hàng cho khách hàng (đặt trước resource để tránh bị che)
    Route::middleware('auth')->get('/orders/history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');

Route::resource('orders', OrderController::class);

// Cart routes
use App\Http\Controllers\CartController;
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');



// Route mua ngay sản phẩm
Route::post('/cart/buy-now/{product}', [App\Http\Controllers\CartController::class, 'buyNow'])->name('cart.buyNow');

// Trang thanh toán riêng biệt
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
});

// Thanh toán các sản phẩm đã chọn trong giỏ hàng
Route::post('/cart/checkout-selected', [App\Http\Controllers\CartController::class, 'checkoutSelected'])->name('cart.checkoutSelected');

// Hủy đơn hàng cho khách hàng
Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');

// Lịch sử mua hàng cho khách hàng
Route::middleware('auth')->get('/orders/history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');

