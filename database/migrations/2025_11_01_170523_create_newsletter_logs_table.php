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
        Schema::create('newsletter_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->timestamp('sent_at');
            $table->integer('zakupki_count')->default(0)->comment('Количество найденных закупок');
            $table->string('status', 20)->default('success')->comment('success/failed');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('newsletter_id');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_logs');
    }
};
