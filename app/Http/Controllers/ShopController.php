<?php

namespace App\Http\Controllers;

use App\Models\ShopCategory;
use App\Models\ShopProduct;
use App\Models\ShopProductView;
use App\Models\ShopProductPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Display shop products listing.
     */
    public function index(Request $request)
    {
        $query = ShopProduct::with('category')
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get all categories with their children
        $categories = ShopCategory::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('shop.index', compact('products', 'categories'));
    }

    /**
     * Display product detail page.
     */
    public function show(Request $request, $slug)
    {
        $product = ShopProduct::with(['category', 'creator'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Log view
        $this->logProductView($product, $request);

        // Increment views count
        $product->incrementViews();

        return view('shop.show', compact('product'));
    }

    /**
     * Purchase a product.
     */
    public function purchase(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Для покупки необходимо авторизоваться');
        }

        $product = ShopProduct::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $user = Auth::user();

        // Check if user has enough balance
        if ($user->balance < $product->price) {
            return back()->with('error', 'Недостаточно средств на балансе. Пополните баланс.');
        }

        DB::beginTransaction();

        try {
            // Deduct balance
            $user->balance -= $product->price;
            $user->save();

            // Create purchase record
            ShopProductPurchase::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'price' => $product->price,
                'status' => 'completed',
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => -$product->price,
                'description' => "Покупка товара: {$product->name}",
                'balance_after' => $user->balance,
            ]);

            // Increment purchases count
            $product->incrementPurchases();

            DB::commit();

            \Log::info('[SHOP] Product purchased', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => $product->price,
            ]);

            return redirect()->route('shop.show', $product->slug)
                ->with('success', "Товар \"{$product->name}\" успешно приобретен!");

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('[SHOP] Purchase failed', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ошибка при покупке товара. Попробуйте позже.');
        }
    }

    /**
     * Log product view.
     */
    private function logProductView(ShopProduct $product, Request $request): void
    {
        ShopProductView::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
