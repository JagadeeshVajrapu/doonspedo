<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bookings', 'reference_no')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedInteger('reference_no')->nullable()->unique()->after('id');
            });
        }

        if (!Schema::hasColumn('driver_registrations', 'display_no')) {
            Schema::table('driver_registrations', function (Blueprint $table) {
                $table->unsignedInteger('display_no')->nullable()->unique()->after('id');
            });
        }

        if (Schema::hasTable('customer_kyc_submissions') && !Schema::hasColumn('customer_kyc_submissions', 'document_label')) {
            Schema::table('customer_kyc_submissions', function (Blueprint $table) {
                $table->string('document_label', 80)->nullable()->after('document_type');
            });
        }

        if (!Schema::hasTable('customer_aadhaar_verifications')) {
            Schema::create('customer_aadhaar_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('aadhaar_hash', 64);
                $table->string('aadhaar_last4', 4);
                $table->string('provider_reference')->nullable();
                $table->string('status', 20)->default('pending');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        $bookingNumber = 1;
        foreach (DB::table('bookings')->orderBy('id')->pluck('id') as $id) {
            DB::table('bookings')->where('id', $id)->whereNull('reference_no')->update([
                'reference_no' => $bookingNumber++,
            ]);
        }

        $driverNumber = 1;
        foreach (DB::table('driver_registrations')->orderBy('id')->pluck('id') as $id) {
            DB::table('driver_registrations')->where('id', $id)->whereNull('display_no')->update([
                'display_no' => $driverNumber++,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_aadhaar_verifications');

        if (Schema::hasColumn('customer_kyc_submissions', 'document_label')) {
            Schema::table('customer_kyc_submissions', function (Blueprint $table) {
                $table->dropColumn('document_label');
            });
        }

        if (Schema::hasColumn('driver_registrations', 'display_no')) {
            Schema::table('driver_registrations', function (Blueprint $table) {
                $table->dropUnique(['display_no']);
                $table->dropColumn('display_no');
            });
        }

        if (Schema::hasColumn('bookings', 'reference_no')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropUnique(['reference_no']);
                $table->dropColumn('reference_no');
            });
        }
    }
};
