<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings') || Schema::hasColumn('bookings', 'arrived_at')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('arrived_at')->nullable()->after('accepted_at');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'arrived_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('arrived_at');
            });
        }
    }
};
