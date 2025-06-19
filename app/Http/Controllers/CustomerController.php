<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->check() && !auth()->user()->is_admin) {
                abort(403, 'Bạn không có quyền truy cập');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('user')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
        'phone' => 'required|string|max:20',
    ]);

    Customer::create($validated);

    return redirect()->route('customers.index')->with('success', 'Thêm khách hàng thành công!');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Customer $customer)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email,' . $customer->id,
        'phone' => 'required|string|max:20',
    ]);

    $customer->update($validated);

    return redirect()->route('customers.index')->with('success', 'Cập nhật thành công!');
}


    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Xóa khách hàng thành công.');
    }
        public function orders(Customer $customer)
    {
        // Giả sử Customer có quan hệ orders
        $orders = $customer->orders()->with('items.product')->paginate(10);
        return view('customers.orders', compact('customer', 'orders'));
    }
}

