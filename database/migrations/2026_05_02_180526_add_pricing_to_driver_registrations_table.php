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
            $table->decimal('min_price', 10, 2)->default(0)->after('vehicle_number');
            $table->decimal('per_km_price', 10, 2)->default(0)->after('min_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn(['min_price', 'per_km_price']);
        });
    }
};
