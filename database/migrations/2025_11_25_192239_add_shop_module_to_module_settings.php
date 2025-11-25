<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('module_settings')->insert([
            'module_key' => 'shop',
            'module_name' => 'Магазин',
            'description' => 'Модуль интернет-магазина с товарами и покупками',
            'is_enabled' => true,
            'settings_route' => null,
            'sort_order' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('module_settings')->where('module_key', 'shop')->delete();
    }
};
