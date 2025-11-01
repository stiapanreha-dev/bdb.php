<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('newsletter_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, time
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('newsletter_settings')->insert([
            [
                'key' => 'send_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Включить автоматическую рассылку',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'send_interval_minutes',
                'value' => '180',
                'type' => 'integer',
                'description' => 'Интервал отправки рассылок (в минутах)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'renew_time',
                'value' => '00:00',
                'type' => 'time',
                'description' => 'Время ежедневного продления подписок (HH:MM)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'renew_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Включить автоматическое продление подписок',
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
        Schema::dropIfExists('newsletter_settings');
    }
};
