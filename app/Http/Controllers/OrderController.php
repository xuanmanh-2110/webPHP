<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $adminRoutes = ['index', 'create', 'store', 'destroy', 'cancel'];
            $action = $request->route()->getActionMethod();

            if (in_array($action, $adminRoutes) && auth()->check() && !auth()->user()->is_admin) {
                abort(403, 'Bạn không có quyền truy cập');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $orders = Order::with('customer', 'items.product')->orderByDesc('created_at')->paginate(10);
        return view('orders.index', compact('orders'));
    }
    public function create()
{
    $customers = Customer::all();
    $products = Product::all();
    return view('orders.create', compact('customers', 'products'));
}

public function store(Request $request)
{
    $request->validate([
        'customer_id' => 'required',
        'products' => 'required|array',
        'quantities' => 'required|array',
    ]);

    $order = Order::create([
        'customer_id' => $request->customer_id,
        'total_price' => 0, // sẽ tính sau
    ]);

    $total = 0;
    foreach ($request->products as $index => $product_id) {
        $product = Product::findOrFail($product_id);
        $qty = $request->quantities[$index];
        $price = $product->price;
        $total += $price * $qty;

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product_id,
            'quantity' => $qty,
            'price' => $price,
        ]);
    }

    $order->update(['total_price' => $total]);

    return redirect()->route('orders.index')->with('success', 'Đã tạo đơn hàng.');
}
public function show($id)
{
    $order = Order::findOrFail($id);
    return view('orders.show', compact('order'));
}
// Lịch sử mua hàng cho khách hàng
public function history(Request $request)
{
    $user = \Auth::user();
    $customer = \App\Models\Customer::where('email', $user->email)->first();
    if (!$customer) {
        return view('orders.history', ['orders' => collect(), 'filters' => []]);
    }

    $query = \App\Models\Order::where('customer_id', $customer->id);

    // Lọc theo trạng thái
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    // Lọc theo khoảng thời gian
    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }
    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    $orders = $query->with('items.product')->orderByDesc('created_at')->paginate(10);
    $filters = $request->only(['status', 'from', 'to']);
    return view('orders.history', compact('orders', 'filters'));
}
// Hủy đơn hàng cho khách hàng
    public function cancel($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        // Giả sử trạng thái lưu ở trường 'status', các trạng thái: 'pending', 'processing', 'shipped', 'cancelled'
        if (in_array($order->status ?? 'pending', ['pending', 'processing'])) {
            $order->status = 'cancelled';
            $order->save();
            return redirect()->route('orders.show', $order->id)->with('success', 'Đơn hàng đã được hủy thành công.');
        }
        return redirect()->route('orders.show', $order->id)->with('error', 'Đơn hàng đã được xử lý hoặc giao, không thể hủy.');
    }


    /**
     * Xóa đơn hàng
     */
    public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Đã xóa đơn hàng thành công!');
    }
}
