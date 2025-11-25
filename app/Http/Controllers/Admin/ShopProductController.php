<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopProductPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShopProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = ShopProduct::with(['category', 'creator']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Show trashed products
        if ($request->filled('trashed') && $request->trashed === '1') {
            $query->onlyTrashed();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = ShopCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.shop.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ShopCategory::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:shop_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shop_products,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();
        $validated['views_count'] = 0;
        $validated['purchases_count'] = 0;

        // Parse description JSON from Editor.js
        if ($request->filled('description')) {
            $validated['description'] = json_decode($request->description, true);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('shop/products', $filename, 'public');
            $validated['image'] = $path;
        }

        ShopProduct::create($validated);

        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Товар успешно создан');
    }

    /**
     * Display the specified product with purchase history.
     */
    public function show(ShopProduct $product)
    {
        $product->load(['category', 'creator', 'purchases.user']);

        $purchases = $product->purchases()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.shop.products.show', compact('product', 'purchases'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = ShopProduct::withTrashed()->findOrFail($id);

        $categories = ShopCategory::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = ShopProduct::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:shop_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shop_products,slug,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Parse description JSON from Editor.js
        if ($request->filled('description')) {
            $validated['description'] = json_decode($request->description, true);
        } else {
            $validated['description'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('shop/products', $filename, 'public');
            $validated['image'] = $path;
        }

        // Handle image deletion
        if ($request->has('delete_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Товар успешно обновлён');
    }

    /**
     * Remove the specified product from storage (soft delete).
     */
    public function destroy(ShopProduct $product)
    {
        $product->delete();

        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Товар успешно удалён');
    }

    /**
     * Restore a soft deleted product.
     */
    public function restore($id)
    {
        $product = ShopProduct::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Товар успешно восстановлен');
    }

    /**
     * Display sales statistics.
     */
    public function statistics()
    {
        // Total revenue
        $totalRevenue = ShopProductPurchase::where('status', 'completed')
            ->sum('price');

        // Total sales count
        $totalSales = ShopProductPurchase::where('status', 'completed')->count();

        // Average check
        $averageCheck = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Total views
        $totalViews = ShopProduct::sum('views_count');

        // Conversion rate (purchases / views)
        $conversionRate = $totalViews > 0 ? ($totalSales / $totalViews) * 100 : 0;

        // Sales by day (last 30 days)
        $salesByDay = ShopProductPurchase::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(price) as revenue')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Top 10 products by sales
        $topProducts = ShopProduct::withCount(['purchases' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withSum(['purchases' => function ($query) {
                $query->where('status', 'completed');
            }], 'price')
            ->orderByDesc('purchases_count')
            ->limit(10)
            ->get();

        // Products with conversion data
        $productsWithConversion = ShopProduct::where('views_count', '>', 0)
            ->withCount(['purchases' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderByRaw('(purchases_count * 100.0 / views_count) DESC')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $product->conversion_rate = $product->views_count > 0
                    ? round(($product->purchases_count / $product->views_count) * 100, 2)
                    : 0;
                return $product;
            });

        return view('admin.shop.statistics', compact(
            'totalRevenue',
            'totalSales',
            'averageCheck',
            'totalViews',
            'conversionRate',
            'salesByDay',
            'topProducts',
            'productsWithConversion'
        ));
    }

    /**
     * Display purchase history.
     */
    public function purchases(Request $request)
    {
        $query = ShopProductPurchase::with(['product', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(50);

        $products = ShopProduct::orderBy('name')->get();
        $statuses = ['pending', 'completed', 'canceled'];

        return view('admin.shop.purchases', compact('purchases', 'products', 'statuses'));
    }
}
