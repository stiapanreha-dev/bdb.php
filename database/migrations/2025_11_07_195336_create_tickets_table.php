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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 10)->unique(); // 10-значный уникальный номер
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone', 20); // Телефон с кодом страны
            $table->string('country_code', 5)->default('+7'); // Код страны
            $table->text('subject'); // Тема обращения
            $table->text('message'); // Первое сообщение
            $table->enum('status', ['new', 'in_progress', 'closed'])->default('new');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
