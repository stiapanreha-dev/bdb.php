<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Удаляем индекс category
            $table->dropIndex(['category']);
            // Удаляем колонку category
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Восстанавливаем колонку category
            $table->string('category')->nullable()->after('type');
            // Восстанавливаем индекс
            $table->index('category');
        });
    }
};
