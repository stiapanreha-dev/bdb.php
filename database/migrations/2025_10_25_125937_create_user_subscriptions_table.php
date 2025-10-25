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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Пользователь
            $table->foreignId('tariff_id')->constrained()->onDelete('restrict'); // Тариф
            $table->timestamp('starts_at'); // Дата начала действия подписки
            $table->timestamp('expires_at'); // Дата окончания подписки
            $table->boolean('is_active')->default(true); // Активна ли подписка
            $table->decimal('paid_amount', 10, 2); // Сумма оплаты (сохраняем на момент покупки)
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index('user_id');
            $table->index('expires_at');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
