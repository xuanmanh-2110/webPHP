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
        $cart = session()->get('cart', []);
        $selected = $request->query('selected');
        if ($selected) {
            $ids = explode(',', $selected);
            $cart = array_filter($cart, function($v, $k) use ($ids) {
                return in_array($k, $ids);
            }, ARRAY_FILTER_USE_BOTH);
        }
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        $total = 0;
        foreach ($cart as $id => $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $user = Auth::user();
        return view('checkout', compact('cart', 'total', 'user'));
    }

    // Xử lý đặt hàng
    public function process(Request $request)
    {
// Tìm hoặc tạo khách hàng tương ứng với user hiện tại
        $user = \Auth::user();
        $customer = \App\Models\Customer::firstOrCreate(
            ['email' => $user->email],
            ['name' => $user->name, 'phone' => $request->phone]
        );
        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        $total = 0;
        foreach ($cart as $id => $item) {
            $product = Product::findOrFail($id);
            $qty = $item['quantity'];
            $price = $product->price;
            $total += $price * $qty;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $qty,
                'price' => $price,
            ]);
        }

        $order->update(['total_amount' => $total]);
        session()->forget('cart');

        return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công!');
    }
}