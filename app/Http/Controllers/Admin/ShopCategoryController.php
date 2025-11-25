<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = ShopCategory::with(['parent', 'children', 'products'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $parentCategories = ShopCategory::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shop_categories,slug',
            'parent_id' => 'nullable|exists:shop_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('shop/categories', $filename, 'public');
            $validated['image'] = $path;
        }

        ShopCategory::create($validated);

        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Категория успешно создана');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ShopCategory $category)
    {
        $parentCategories = ShopCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.shop.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, ShopCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shop_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:shop_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Prevent category from being its own parent
        if ($validated['parent_id'] == $category->id) {
            return back()->with('error', 'Категория не может быть родителем самой себя');
        }

        // Prevent category from being a child of its own children
        if ($validated['parent_id']) {
            $childIds = $category->children()->pluck('id')->toArray();
            if (in_array($validated['parent_id'], $childIds)) {
                return back()->with('error', 'Нельзя выбрать дочернюю категорию в качестве родительской');
            }
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $image = $request->file('image');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('shop/categories', $filename, 'public');
            $validated['image'] = $path;
        }

        // Handle image deletion
        if ($request->has('delete_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $validated['image'] = null;
        }

        $category->update($validated);

        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Категория успешно обновлена');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(ShopCategory $category)
    {
        // Check for subcategories
        $childrenCount = $category->children()->count();
        if ($childrenCount > 0) {
            return back()->with('error',
                "Невозможно удалить категорию. Есть {$childrenCount} подкатегорий.");
        }

        // Check for products
        $productsCount = $category->products()->count();
        if ($productsCount > 0) {
            return back()->with('error',
                "Невозможно удалить категорию. Есть {$productsCount} товаров.");
        }

        // Delete image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Категория успешно удалена');
    }
}
