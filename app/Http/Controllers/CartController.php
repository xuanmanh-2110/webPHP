<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Tính giá thực tế của sản phẩm (có giảm giá cho 5 sản phẩm mới nhất chỉ khi mua 1 sản phẩm)
     */
    private function getActualPrice($product, $quantity = 1)
    {
        // Lấy 5 sản phẩm mới nhất
        $latestProductIds = Product::latest()->take(5)->pluck('id')->toArray();
        $isLatestProduct = in_array($product->id, $latestProductIds);
        
        // Chỉ giảm giá khi là latest product VÀ số lượng = 1
        if ($isLatestProduct && $quantity == 1) {
            return $product->price * 0.8; // Giảm 20%
        }
        
        return $product->price; // Giá gốc
    }
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $id => $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            // Nếu sản phẩm đã có trong giỏ, cập nhật số lượng và tính lại giá
            $newQuantity = $cart[$id]['quantity'] + $quantity;
            $actualPrice = $this->getActualPrice($product, $newQuantity);
            $cart[$id]['quantity'] = $newQuantity;
            $cart[$id]['price'] = $actualPrice;
        } else {
            // Nếu sản phẩm chưa có trong giỏ, thêm mới
            $actualPrice = $this->getActualPrice($product, $quantity);
            $cart[$id] = [
                "name" => $product->name,
                "price" => $actualPrice,
                "quantity" => $quantity
            ];
        }

        session()->put('cart', $cart);
        
        // Kiểm tra nếu request là AJAX thì trả về JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm ' . $quantity . ' sản phẩm vào giỏ hàng!'
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            // Tìm sản phẩm và tính lại giá dựa trên số lượng mới
            $product = Product::findOrFail($id);
            $newQuantity = $request->quantity;
            $actualPrice = $this->getActualPrice($product, $newQuantity);
            
            $cart[$id]['quantity'] = $newQuantity;
            $cart[$id]['price'] = $actualPrice;
            session()->put('cart', $cart);

            $itemTotal = $cart[$id]['price'] * $cart[$id]['quantity'];
            $cartTotal = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng!',
                'item_total_formatted' => number_format($itemTotal, 0, ',', '.') . ' VND',
                'cart_total_formatted' => number_format($cartTotal, 0, ',', '.') . ' VND'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.'], 404);
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            $cartTotal = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cart_total_formatted' => number_format($cartTotal, 0, ',', '.') . ' VND'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.'], 404);
    }
// Thanh toán các sản phẩm đã chọn trong giỏ hàng
    public function checkoutSelected(Request $request)
    {
        $selected = $request->input('selected_products', []);
        if (empty($selected)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn sản phẩm để thanh toán!');
        }
        return redirect()->route('checkout.show', ['selected' => implode(',', $selected)]);
    }
    public function buyNow(Request $request, $product)
    {
        $product = \App\Models\Product::findOrFail($product);
        $quantity = $request->input('quantity', 1);
        $actualPrice = $this->getActualPrice($product, $quantity);
        
        // Tạo một cart riêng biệt cho "Mua ngay" mà không ảnh hưởng đến cart hiện tại
        $buyNowCart = [
            $product->id => [
                "name" => $product->name,
                "price" => $actualPrice,
                "quantity" => $quantity
            ]
        ];
        
        // Lưu cart "Mua ngay" vào session riêng biệt
        session()->put('buy_now_cart', $buyNowCart);
        
        // Chuyển hướng đến trang thanh toán với flag buy_now
        return redirect()->route('checkout.show', ['buy_now' => true]);
    }
}