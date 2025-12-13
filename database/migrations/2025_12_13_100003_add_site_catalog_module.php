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
            'module_key' => 'site_catalog',
            'module_name' => 'Каталог сайтов',
            'description' => 'Модуль для добавления и просмотра каталога сайтов с модерацией',
            'is_enabled' => true,
            'settings_route' => null,
            'sort_order' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('module_settings')->where('module_key', 'site_catalog')->delete();
    }
};
