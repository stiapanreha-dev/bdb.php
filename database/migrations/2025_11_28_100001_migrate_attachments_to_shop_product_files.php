<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing attachments to shop_product_files table
        $products = DB::table('shop_products')
            ->whereNotNull('attachment')
            ->where('attachment', '!=', '')
            ->get();

        foreach ($products as $product) {
            // Calculate file size
            $filePath = storage_path('app/private/' . $product->attachment);
            $size = file_exists($filePath) ? filesize($filePath) : 0;

            DB::table('shop_product_files')->insert([
                'product_id' => $product->id,
                'file_path' => $product->attachment,
                'original_name' => $product->attachment_name ?? basename($product->attachment),
                'size' => $size,
                'sort_order' => 0,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // Move files back to attachment column
        $files = DB::table('shop_product_files')
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('product_id');

        foreach ($files as $productId => $productFiles) {
            $firstFile = $productFiles->first();

            DB::table('shop_products')
                ->where('id', $productId)
                ->update([
                    'attachment' => $firstFile->file_path,
                    'attachment_name' => $firstFile->original_name,
                ]);
        }

        // Delete all records from shop_product_files
        DB::table('shop_product_files')->truncate();
    }
};
