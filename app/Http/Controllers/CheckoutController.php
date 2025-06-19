<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    // Hiển thị trang thanh toán
    public function show(Request $request)
    {
        // Kiểm tra nếu đây là "Mua ngay"
        $isBuyNow = $request->query('buy_now', false);
        
        if ($isBuyNow) {
            // Sử dụng cart riêng biệt cho "Mua ngay"
            $cart = session()->get('buy_now_cart', []);
        } else {
            // Sử dụng cart thông thường
            $cart = session()->get('cart', []);
            $selected = $request->query('selected');
            if ($selected) {
                $ids = explode(',', $selected);
                $cart = array_filter($cart, function($v, $k) use ($ids) {
                    return in_array($k, $ids);
                }, ARRAY_FILTER_USE_BOTH);
            }
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        $total = 0;
        foreach ($cart as $id => $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $user = Auth::user();
        
        // Ưu tiên sử dụng thông tin từ User profile
        $userAddress = null;
        $userPhone = null;
        if ($user->address) {
            $userAddress = $user->address;
        }
        if ($user->phone) {
            $userPhone = $user->phone;
        }
        
        // Fallback: Lấy thông tin giao hàng từ đơn hàng gần nhất nếu user chưa có thông tin
        $customer = \App\Models\Customer::where('email', $user->email)->first();
        $lastOrder = null;
        if ($customer && (!$userAddress || !$userPhone)) {
            $lastOrder = Order::where('customer_id', $customer->id)
                            ->whereNotNull('address')
                            ->whereNotNull('phone')
                            ->orderBy('created_at', 'desc')
                            ->first();
        }
        
        return view('checkout', compact('cart', 'total', 'user', 'lastOrder', 'userAddress', 'userPhone', 'isBuyNow'));
    }

    // Xử lý đặt hàng
    public function process(Request $request)
    {
        $validationRules = [
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,bank_transfer',
        ];

        // Thêm validation cho thông tin ngân hàng nếu chọn chuyển khoản
        if ($request->payment_method === 'bank_transfer') {
            $validationRules['customer_bank_name'] = 'required|string|max:100';
            $validationRules['customer_account_number'] = 'required|string|max:50';
        }

        $request->validate($validationRules);

        // Kiểm tra nếu đây là "Mua ngay"
        $isBuyNow = $request->input('is_buy_now', false);
        
        if ($isBuyNow) {
            $cart = session()->get('buy_now_cart', []);
        } else {
            $cart = session()->get('cart', []);
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Tìm hoặc tạo khách hàng tương ứng với user hiện tại
        $user = \Auth::user();
        $customer = \App\Models\Customer::firstOrCreate(
            ['email' => $user->email],
            ['name' => $user->name, 'phone' => $request->phone]
        );
        
        // Cập nhật thông tin số điện thoại mới nhất
        $customer->update(['phone' => $request->phone]);

        $orderData = [
            'customer_id' => $customer->id,
            'total_amount' => 0,
            'address' => $request->address,
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
        ];

        // Thêm thông tin ngân hàng nếu chọn chuyển khoản
        if ($request->payment_method === 'bank_transfer') {
            $orderData['customer_bank_name'] = $request->customer_bank_name;
            $orderData['customer_account_number'] = $request->customer_account_number;
        }

        $order = Order::create($orderData);
        
        // Debug log
        \Log::info('Order created with phone: ' . $request->phone);

        $total = 0;
        foreach ($cart as $id => $item) {
            $product = Product::findOrFail($id);
            $qty = $item['quantity'];
            $price = $item['price']; // Sử dụng giá từ cart (đã tính giảm giá)
            $total += $price * $qty;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $qty,
                'price' => $price,
            ]);
        }

        $order->update(['total_amount' => $total]);
        
        // Xóa session cart tương ứng
        if ($isBuyNow) {
            session()->forget('buy_now_cart');
        } else {
            session()->forget('cart');
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');
    }
}