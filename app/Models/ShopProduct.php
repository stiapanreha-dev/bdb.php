<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProduct extends Model
{
    use SoftDeletes;

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
     * Files
     */
    public function files(): HasMany
    {
        return $this->hasMany(ShopProductFile::class, 'product_id')->orderBy('sort_order');
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

    /**
     * Get files count (uses withCount if loaded, otherwise queries)
     */
    public function getFilesCountAttribute(): int
    {
        if (array_key_exists('files_count', $this->attributes)) {
            return (int) $this->attributes['files_count'];
        }
        return $this->files()->count();
    }

    /**
     * Get total files size (uses loaded files if available, otherwise queries)
     */
    public function getTotalFilesSizeAttribute(): int
    {
        if ($this->relationLoaded('files')) {
            return (int) $this->files->sum('size');
        }
        return (int) $this->files()->sum('size');
    }

    /**
     * Get formatted total files size
     */
    public function getFormattedTotalFilesSizeAttribute(): ?string
    {
        $size = $this->total_files_size;

        if ($size === 0) {
            return null;
        }

        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' ГБ';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' МБ';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' КБ';
        } else {
            return $size . ' байт';
        }
    }
}
