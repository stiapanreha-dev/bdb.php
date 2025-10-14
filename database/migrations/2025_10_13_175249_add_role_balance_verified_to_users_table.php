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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->decimal('balance', 10, 2)->default(0.00)->after('password');
            $table->string('role', 20)->default('user')->after('balance');
            $table->boolean('email_verified')->default(false)->after('role');
            $table->boolean('phone_verified')->default(false)->after('email_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'balance', 'role', 'email_verified', 'phone_verified']);
        });
    }
};
