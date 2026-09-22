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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->nullable()->after('pickup_location');
            $table->decimal('pickup_lng', 11, 8)->nullable()->after('pickup_lat');
            $table->decimal('dropoff_lat', 10, 8)->nullable()->after('dropoff_location');
            $table->decimal('dropoff_lng', 11, 8)->nullable()->after('dropoff_lat');
            $table->timestamp('accepted_at')->nullable()->after('status');
            $table->timestamp('picked_up_at')->nullable()->after('accepted_at');
            $table->timestamp('completed_at')->nullable()->after('picked_up_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng', 'accepted_at', 'picked_up_at', 'completed_at', 'cancelled_at']);
        });
    }
};
