<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('upi_id')->nullable();
            $table->string('image_path');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('wallet_recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('driver_registrations')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('upi_qr');
            $table->foreignId('payment_qr_code_id')->nullable()->constrained('payment_qr_codes')->nullOnDelete();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'successful', 'failed', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['driver_id', 'status']);
        });

        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(false);
            $table->decimal('low_balance_threshold', 12, 2)->default(100);
            $table->decimal('min_recharge', 12, 2)->default(10);
            $table->decimal('max_recharge', 12, 2)->default(50000);
            $table->timestamp('effective_from')->nullable();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('balance_before', 12, 2)->nullable()->after('amount');
            $table->decimal('balance_after', 12, 2)->nullable()->after('balance_before');
            $table->string('reference_type')->nullable()->after('reference_id');
            $table->string('category')->nullable()->after('reference_type');
            $table->foreignId('wallet_recharge_id')->nullable()->after('category')->constrained('wallet_recharges')->nullOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unique(['driver_id', 'booking_id', 'category'], 'transactions_driver_booking_category_unique');
            $table->unique('wallet_recharge_id', 'transactions_wallet_recharge_unique');
        });

        DB::table('commission_settings')->insert([
            'type' => 'fixed',
            'amount' => 20,
            'is_active' => true,
            'low_balance_threshold' => 100,
            'min_recharge' => 10,
            'max_recharge' => 50000,
            'effective_from' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $relative = 'payment-qr/test-qr.png';
        Storage::disk('public')->put($relative, $this->testQrPng());

        DB::table('payment_qr_codes')->insert([
            'title' => 'TEST QR',
            'upi_id' => 'test@doonspedo',
            'image_path' => $relative,
            'is_active' => true,
            'is_test' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_wallet_recharge_unique');
            $table->dropUnique('transactions_driver_booking_category_unique');
            $table->dropConstrainedForeignId('wallet_recharge_id');
            $table->dropColumn(['balance_before', 'balance_after', 'reference_type', 'category']);
        });

        Schema::dropIfExists('wallet_recharges');
        Schema::dropIfExists('commission_settings');
        Schema::dropIfExists('payment_qr_codes');
    }

    private function testQrPng(): string
    {
        if (function_exists('imagecreatetruecolor')) {
            $image = imagecreatetruecolor(360, 360);
            $white = imagecolorallocate($image, 255, 255, 255);
            $ink = imagecolorallocate($image, 15, 23, 42);
            $muted = imagecolorallocate($image, 100, 116, 139);
            imagefilledrectangle($image, 0, 0, 359, 359, $white);
            imagerectangle($image, 16, 16, 343, 343, $ink);
            imagestring($image, 5, 130, 150, 'TEST QR', $ink);
            imagestring($image, 3, 70, 190, 'Not a confirmed payment', $muted);
            ob_start();
            imagepng($image);
            $binary = ob_get_clean();
            imagedestroy($image);

            return $binary;
        }

        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAFUlEQVR42mP8z8BQz0AEYBxVSF+FABJADveWkH6oAAAAAElFTkSuQmCC');
    }
};
