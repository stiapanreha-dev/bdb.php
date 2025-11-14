<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductView extends Model
{
    protected $table = 'shop_product_views';

    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    /**
     * User (nullable)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
