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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('site_categories')->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url', 500);
            $table->string('logo')->nullable();
            $table->text('description')->nullable(); // JSON Editor.js format
            $table->json('images')->nullable(); // Additional images
            $table->string('contact_email');
            $table->string('status', 50)->default('pending'); // pending, approved, rejected
            $table->text('moderation_comment')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
