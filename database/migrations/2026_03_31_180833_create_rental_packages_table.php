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
        Schema::create('rental_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('hours');
            $table->integer('distance_km');
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert some default packages
        DB::table('rental_packages')->insert([
            ['name' => '1 Hour / 10KM', 'hours' => 1, 'distance_km' => 10, 'base_price' => 300, 'created_at' => now()],
            ['name' => '2 Hours / 20KM', 'hours' => 2, 'distance_km' => 20, 'base_price' => 550, 'created_at' => now()],
            ['name' => '4 Hours / 40KM', 'hours' => 4, 'distance_km' => 40, 'base_price' => 1000, 'created_at' => now()],
            ['name' => '8 Hours / 80KM', 'hours' => 8, 'distance_km' => 80, 'base_price' => 1800, 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_packages');
    }
};
