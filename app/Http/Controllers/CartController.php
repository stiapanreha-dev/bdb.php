<?php

namespace App\Http\Controllers;

use App\Models\ShopCart;
use App\Models\ShopCartItem;
use App\Models\ShopProduct;
use App\Models\ShopProductPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart = Auth::user()->cart;

        if ($cart) {
            $cart->load('items.product');
        }

        return view('shop.cart', compact('cart'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request, $productId)
    {
        $product = ShopProduct::where('id', $productId)
            ->where('is_active', true)
            ->firstOrFail();

        $user = Auth::user();

        // Check if user already purchased this product
        $alreadyPurchased = ShopProductPurchase::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyPurchased) {
            return back()->with('warning', "Вы уже покупали товар \"{$product->name}\". <a href=\"" . route('shop.my-purchases') . "\">Перейти в историю покупок</a>");
        }

        $cart = $user->getOrCreateCart();

        // Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Product already in cart - do nothing
            return back()->with('info', "Товар \"{$product->name}\" уже в корзине");
        }

        // Create new cart item
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        return back()->with('success', "Товар \"{$product->name}\" добавлен в корзину");
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = Auth::user()->cart;

        if (!$cart) {
            return back()->with('error', 'Корзина не найдена');
        }

        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->quantity = $request->quantity;
        $item->save();

        return back()->with('success', 'Количество обновлено');
    }

    /**
     * Remove item from cart.
     */
    public function remove($itemId)
    {
        $cart = Auth::user()->cart;

        if (!$cart) {
            return back()->with('error', 'Корзина не найдена');
        }

        $item = $cart->items()->where('id', $itemId)->first();

        if ($item) {
            $productName = $item->product->name;
            $item->delete();
            return back()->with('success', "Товар \"{$productName}\" удалён из корзины");
        }

        return back()->with('error', 'Товар не найден в корзине');
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        $cart = Auth::user()->cart;

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Корзина очищена');
    }

    /**
     * Checkout - process the order.
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Корзина пуста');
        }

        // Reload items with products
        $cart->load('items.product');

        // Check all products are active
        foreach ($cart->items as $item) {
            if (!$item->product || !$item->product->is_active) {
                return back()->with('error', "Товар \"{$item->product?->name}\" недоступен");
            }
        }

        $total = $cart->total;

        if ($user->balance < $total) {
            return back()->with('error', 'Недостаточно средств на балансе. Пополните баланс.');
        }

        DB::beginTransaction();

        try {
            // Deduct balance
            $user->balance -= $total;
            $user->save();

            // Create purchase records for each item
            foreach ($cart->items as $item) {
                ShopProductPurchase::create([
                    'product_id' => $item->product->id,
                    'user_id' => $user->id,
                    'price' => $item->product->price,
                    'status' => 'completed',
                ]);

                // Update purchases_count
                $item->product->increment('purchases_count');
            }

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => -$total,
                'description' => "Покупка из корзины: {$cart->item_count} шт.",
                'balance_after' => $user->balance,
            ]);

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            \Log::info('[CART] Checkout successful', [
                'user_id' => $user->id,
                'total' => $total,
                'items_count' => $cart->item_count,
            ]);

            return redirect()->route('shop.my-purchases')
                ->with('success', 'Заказ успешно оформлен!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('[CART] Checkout failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ошибка при оформлении заказа. Попробуйте позже.');
        }
    }
}
