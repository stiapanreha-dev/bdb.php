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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'supplier', 'buyer', 'dealer'
            $table->string('category')->nullable();
            $table->string('title');
            $table->text('description');
            $table->boolean('register_as_purchase')->default(false); // чекбокс "Зарегистрировать в закупках"
            $table->foreignId('company_id')->nullable()->constrained('users')->onDelete('set null'); // связь с компанией пользователя
            $table->string('status')->default('active'); // active, inactive, moderation
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('category');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
