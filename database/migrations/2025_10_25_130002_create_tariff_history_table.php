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
        Schema::create('tariff_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_id')->constrained()->onDelete('cascade'); // Тариф
            $table->string('field_name'); // Название изменённого поля (name, price, duration_days)
            $table->text('old_value')->nullable(); // Старое значение
            $table->text('new_value')->nullable(); // Новое значение
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null'); // Кто изменил (админ)
            $table->timestamp('changed_at'); // Когда изменено
            $table->timestamps();

            // Индексы
            $table->index('tariff_id');
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff_history');
    }
};
