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
        'attachment',
        'attachment_name',
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

    /**
     * Get attachment file size
     */
    public function getAttachmentSizeAttribute(): ?int
    {
        if (!$this->attachment) {
            return null;
        }

        // Attachments stored in private storage (local disk -> storage/app/private/)
        $path = storage_path('app/private/' . $this->attachment);

        if (file_exists($path)) {
            return filesize($path);
        }

        return null;
    }

    /**
     * Get formatted attachment file size
     */
    public function getFormattedAttachmentSizeAttribute(): ?string
    {
        $size = $this->attachment_size;

        if ($size === null) {
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
