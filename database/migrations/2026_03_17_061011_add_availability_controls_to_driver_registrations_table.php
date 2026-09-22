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
            $table->json('working_hours')->nullable()->after('is_online');
            $table->json('ride_preferences')->nullable()->after('working_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn(['working_hours', 'ride_preferences']);
        });
    }
};
