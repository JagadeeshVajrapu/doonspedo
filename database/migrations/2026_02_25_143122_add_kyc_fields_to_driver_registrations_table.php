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
            $table->string('profile_image')->nullable()->after('city');
            $table->string('license_image')->nullable()->after('profile_image');
            $table->string('aadhaar_image')->nullable()->after('license_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_registrations', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'license_image', 'aadhaar_image']);
        });
    }
};
