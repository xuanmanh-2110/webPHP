<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Nếu là khách hàng và có search, chuyển hướng sang /shop
        if (!auth()->check() || !auth()->user()->is_admin) {
            if ($request->filled('search')) {
                return redirect()->route('products.shop', ['search' => $request->query('search')]);
            }
        }
        $query = Product::query();
        $search = $request->query('search');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $products = $query->latest()->paginate(10);
        return view('products.index', compact('products', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'category']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
         $randomProducts = Product::where('id', '!=', $product->id)->inRandomOrder()->limit(5)->get();
         return view('products.show', compact('product', 'randomProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'category']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    // Gợi ý sản phẩm cho tìm kiếm AJAX
    public function suggest(\Illuminate\Http\Request $request)
    {
        $q = $request->query('q');
        if (!$q || mb_strlen($q) < 2) return response()->json([]);
        $products = \App\Models\Product::where('name', 'like', '%' . $q . '%')->limit(10)->get(['id', 'name']);
        return response()->json($products);
    }

    // Trang danh sách sản phẩm dành cho khách hàng
    public function shop(\Illuminate\Http\Request $request)
    {
        $query = Product::query();
        $search = $request->query('search');
        $category = $request->query('category');
        $price = $request->query('price');
        $sort = $request->query('sort');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($price) {
            if ($price == '1') $query->where('price', '<', 200000);
            if ($price == '2') $query->whereBetween('price', [200000, 500000]);
            if ($price == '3') $query->where('price', '>', 500000);
        }
        if ($sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(20)->appends($request->query());
        $categoryList = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('products.shop', compact('products', 'search', 'categoryList', 'category', 'price', 'sort'));
    }

    public function __construct()
    {
        $this->middleware('auth');

        // Chỉ kiểm tra quyền admin với các chức năng quản lý (trừ index, show)
        $this->middleware(function ($request, $next) {
            $adminRoutes = ['index', 'create', 'store', 'edit', 'update', 'destroy'];
            $action = $request->route()->getActionMethod();
            // Nếu là người dùng thường, không cho vào bất kỳ route nào của products ngoài index/show/shop
            if (auth()->check() && !auth()->user()->is_admin && !in_array($action, ['index', 'show', 'shop'])) {
                abort(403, 'Bạn không có quyền truy cập');
            }
            return $next($request);
        });
    }


}


