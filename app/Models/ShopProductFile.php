<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductFile extends Model
{
    protected $table = 'shop_product_files';

    protected $fillable = [
        'product_id',
        'file_path',
        'original_name',
        'size',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id');
    }

    /**
     * Get full path to file
     */
    public function getFullPathAttribute(): string
    {
        return storage_path('app/private/' . $this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;

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

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    /**
     * Get file icon class (Bootstrap Icons)
     */
    public function getIconClassAttribute(): string
    {
        $icons = [
            'pdf' => 'bi-file-earmark-pdf',
            'doc' => 'bi-file-earmark-word',
            'docx' => 'bi-file-earmark-word',
            'xls' => 'bi-file-earmark-excel',
            'xlsx' => 'bi-file-earmark-excel',
            'ppt' => 'bi-file-earmark-ppt',
            'pptx' => 'bi-file-earmark-ppt',
            'zip' => 'bi-file-earmark-zip',
            'rar' => 'bi-file-earmark-zip',
            '7z' => 'bi-file-earmark-zip',
            'jpg' => 'bi-file-earmark-image',
            'jpeg' => 'bi-file-earmark-image',
            'png' => 'bi-file-earmark-image',
            'gif' => 'bi-file-earmark-image',
            'mp3' => 'bi-file-earmark-music',
            'wav' => 'bi-file-earmark-music',
            'mp4' => 'bi-file-earmark-play',
            'avi' => 'bi-file-earmark-play',
            'txt' => 'bi-file-earmark-text',
            'csv' => 'bi-file-earmark-spreadsheet',
        ];

        return $icons[$this->extension] ?? 'bi-file-earmark';
    }
}
