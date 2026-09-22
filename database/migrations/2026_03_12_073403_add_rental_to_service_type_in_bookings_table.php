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
        // MySQL-only: SQLite stores enums as strings and does not support MODIFY
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN service_type ENUM('ride', 'parcel', 'freight', 'rental') DEFAULT 'ride'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN service_type ENUM('ride', 'parcel', 'freight') DEFAULT 'ride'");
    }
};
