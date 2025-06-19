<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

use App\Models\Product;
Route::get('/', function () {
    $products = Product::latest()->take(10)->get();
    $latestProducts = Product::latest()->take(5)->get();
    return view('welcome', compact('products', 'latestProducts'));
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return 'Chào mừng bạn đến Dashboard!';
})->middleware('auth');

Route::resource('products', ProductController::class)->except(['show']);
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::get('/products/suggest', [App\Http\Controllers\ProductController::class, 'suggest'])->name('products.suggest');
Route::get('/shop', [App\Http\Controllers\ProductController::class, 'shop'])->name('products.shop');

Route::middleware('auth')->group(function () {
    Route::get('/products/{product}/analytics', [ProductController::class, 'analytics'])->name('products.analytics');
    
    // Review routes
    Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('reviews.store');
    Route::put('/reviews/{review}', [ProductController::class, 'updateReview'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ProductController::class, 'deleteReview'])->name('reviews.delete');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
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

// Cập nhật trạng thái đơn hàng (chỉ admin)
Route::middleware('auth')->post('/orders/{order}/update-status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

// Lịch sử mua hàng cho khách hàng
Route::middleware('auth')->get('/orders/history', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');

