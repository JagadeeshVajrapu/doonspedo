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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('driver_registrations')->onDelete('set null');
            $table->foreignId('vehicle_category_id')->nullable()->constrained('vehicle_categories')->onDelete('set null');
            $table->enum('service_type', ['ride', 'parcel', 'freight'])->default('ride');
            $table->enum('status', ['pending', 'accepted', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->string('distance')->nullable();
            $table->decimal('fare', 8, 2)->nullable();
            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('pending');
            $table->text('notes')->nullable();
            $table->text('parcel_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
