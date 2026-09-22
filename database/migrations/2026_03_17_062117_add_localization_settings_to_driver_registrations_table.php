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
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->string('locale')->default('en')->after('ride_preferences');
            $table->string('currency_code')->default('INR')->after('locale');
            $table->string('theme')->default('light')->after('currency_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn(['locale', 'currency_code', 'theme']);
        });
    }
};
