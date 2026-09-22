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
        // First, we need to change the enum to allow 'pending'
        // Since SQLite (if used) or some MySQL versions don't like altering enums directly,
        // we'll just change it to a string for maximum flexibility, or update the enum.
        
        Schema::table('driver_subscriptions', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
            $table->string('payment_proof')->nullable()->after('price');
            $table->text('admin_note')->nullable()->after('payment_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'admin_note']);
        });
    }
};
