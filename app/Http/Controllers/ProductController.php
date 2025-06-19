<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price']);

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
        // Lấy 5 sản phẩm mới nhất để kiểm tra xem sản phẩm hiện tại có trong danh sách không
        $latestProductIds = Product::latest()->take(5)->pluck('id')->toArray();
        $isLatestProduct = in_array($product->id, $latestProductIds);
        
        $randomProducts = Product::where('id', '!=', $product->id)->inRandomOrder()->limit(4)->get();
        $latestProduct = Product::where('id', '!=', $product->id)->orderBy('created_at', 'desc')->first();
        
        // Load reviews cho sản phẩm này
        $reviews = $product->reviews()->with('user')->newest()->get();
        
        // Tính toán điểm đánh giá trung bình và số lượng đánh giá
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
        
        return view('products.show', compact('product', 'randomProducts', 'latestProduct', 'isLatestProduct', 'reviews', 'totalReviews', 'averageRating'));
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
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price']);

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
        $price = $request->query('price');
        $sort = $request->query('sort');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
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
        
        // Lấy 5 sản phẩm mới nhất để xác định sản phẩm nào được giảm giá
        $latestProductIds = Product::latest()->take(5)->pluck('id')->toArray();
        
        return view('products.shop', compact('products', 'search', 'price', 'sort', 'latestProductIds'));
    }

    /**
     * Store a new review for a product.
     */
    public function storeReview(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá sản phẩm'
            ], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
            'reviewer_name' => 'required|string|max:255'
        ]);

        $review = $product->reviews()->create([
            'user_id' => Auth::id(),
            'reviewer_name' => $request->reviewer_name,
            'rating' => $request->rating,
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá đã được thêm thành công!',
            'review' => $review->load('user')
        ]);
    }

    /**
     * Update a review.
     */
    public function updateReview(Request $request, Review $review)
    {
        try {
            // Kiểm tra quyền sửa (chỉ người tạo review mới được sửa)
            if (!Auth::check() || $review->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền sửa đánh giá này!'
                ], 403);
            }

            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'content' => 'required|string|max:1000'
            ]);

            $review->update([
                'rating' => $request->rating,
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được cập nhật!',
                'review' => $review->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Update review error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a review.
     */
    public function deleteReview(Review $review)
    {
        // Kiểm tra quyền xóa (chỉ người tạo review mới được xóa)
        if (!Auth::check() || $review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa đánh giá này!'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá đã được xóa!'
        ]);
    }

    /**
     * Show analytics for a product (Admin only).
     */
    public function analytics(Product $product)
    {
        // Kiểm tra quyền admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }

        // Lấy tất cả đánh giá cho sản phẩm
        $reviews = $product->reviews()->with('user')->latest()->paginate(10);
        $totalReviews = $product->reviews()->count();
        $averageRating = $product->reviews()->avg('rating');

        // Phân tích phân bố đánh giá
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = $product->reviews()->where('rating', $i)->count();
        }

        // Thống kê đơn hàng từ OrderItem
        $orderItems = OrderItem::where('product_id', $product->id)->with('order')->get();
        $totalOrders = $orderItems->count();
        $totalRevenue = $orderItems->sum(function($item) {
            return $item->quantity * $item->price;
        });

        // Tính tỉ lệ mua lại (số lần mua / số khách hàng duy nhất)
        $uniqueCustomers = $orderItems->pluck('order.customer_id')->filter()->unique()->count();
        $repurchaseRate = $uniqueCustomers > 0 ? (($totalOrders - $uniqueCustomers) / $uniqueCustomers) * 100 : 0;

        // Tính trung bình đơn hàng/tháng (dựa trên 6 tháng gần nhất)
        $recentOrders = OrderItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();
        $avgMonthlyOrders = $recentOrders / 6;

        // Tỉ lệ có đánh giá
        $reviewRate = $totalOrders > 0 ? ($totalReviews / $totalOrders) * 100 : 0;

        return view('products.analytics', compact(
            'product',
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingDistribution',
            'totalOrders',
            'totalRevenue',
            'repurchaseRate',
            'avgMonthlyOrders',
            'reviewRate'
        ));
    }

    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'shop']);

        // Chỉ kiểm tra quyền admin với các chức năng quản lý (trừ index, show)
        $this->middleware(function ($request, $next) {
            $adminRoutes = ['create', 'store', 'edit', 'update', 'destroy'];
            $action = $request->route()->getActionMethod();
            // Nếu là người dùng thường, không cho vào các route quản lý
            if (auth()->check() && !auth()->user()->is_admin && in_array($action, $adminRoutes)) {
                abort(403, 'Bạn không có quyền truy cập');
            }
            return $next($request);
        });
    }


}


