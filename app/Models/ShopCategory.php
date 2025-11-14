<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopCategory extends Model
{
    protected $table = 'shop_categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'parent_id');
    }

    /**
     * Child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(ShopCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(ShopProduct::class, 'category_id');
    }

    /**
     * Get active products count
     */
    public function activeProductsCount(): int
    {
        return $this->products()->where('is_active', true)->count();
    }
}
