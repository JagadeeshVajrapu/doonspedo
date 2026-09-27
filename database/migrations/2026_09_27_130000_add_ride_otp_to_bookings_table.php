<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'ride_otp')) {
                $table->string('ride_otp', 6)->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'ride_otp_verified_at')) {
                $table->timestamp('ride_otp_verified_at')->nullable()->after('ride_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'ride_otp_verified_at')) {
                $table->dropColumn('ride_otp_verified_at');
            }
            if (Schema::hasColumn('bookings', 'ride_otp')) {
                $table->dropColumn('ride_otp');
            }
        });
    }
};
