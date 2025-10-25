<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("tariffs", function (Blueprint $table) {
            $table->id();
            $table->string("name"); // Название тарифа
            $table->integer("duration_days"); // Длительность в днях
            $table->decimal("price", 10, 2); // Цена
            $table->boolean("is_active")->default(true); // Активен ли тариф
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("tariffs");
    }
};
