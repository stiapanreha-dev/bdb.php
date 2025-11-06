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
        Schema::create('module_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module_key')->unique();
            $table->string('module_name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->string('settings_route')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Добавляем базовые модули
        DB::table('module_settings')->insert([
            [
                'module_key' => 'announcements',
                'module_name' => 'Доска объявлений',
                'description' => 'Модуль для размещения и просмотра объявлений пользователей',
                'is_enabled' => true,
                'settings_route' => null,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'articles',
                'module_name' => 'Статьи',
                'description' => 'Модуль для публикации и чтения статей',
                'is_enabled' => true,
                'settings_route' => null,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'news',
                'module_name' => 'Новости',
                'description' => 'Модуль новостей (доступен только администраторам)',
                'is_enabled' => true,
                'settings_route' => null,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'ideas',
                'module_name' => 'Идеи',
                'description' => 'Модуль для отправки идей и предложений',
                'is_enabled' => true,
                'settings_route' => null,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'newsletters',
                'module_name' => 'Рассылки',
                'description' => 'Модуль email-рассылок закупок по ключевым словам',
                'is_enabled' => true,
                'settings_route' => '/admin/newsletter-settings',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_settings');
    }
};
