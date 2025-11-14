<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductPurchase extends Model
{
    protected $table = 'shop_product_purchases';

    protected $fillable = [
        'product_id',
        'user_id',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    /**
     * User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if purchase is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if purchase is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}
