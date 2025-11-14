<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopProduct extends Model
{
    protected $table = 'shop_products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'image',
        'price',
        'is_active',
        'created_by',
        'views_count',
        'purchases_count',
    ];

    protected $casts = [
        'description' => 'array', // Editor.js JSON
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'purchases_count' => 'integer',
    ];

    /**
     * Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id');
    }

    /**
     * Creator (admin)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Views
     */
    public function views(): HasMany
    {
        return $this->hasMany(ShopProductView::class, 'product_id');
    }

    /**
     * Purchases
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(ShopProductPurchase::class, 'product_id');
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment purchases count
     */
    public function incrementPurchases(): void
    {
        $this->increment('purchases_count');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' ₽';
    }
}
