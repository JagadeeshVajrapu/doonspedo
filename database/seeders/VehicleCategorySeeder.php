<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\VehicleCategory::insert([
            [
                'name' => 'Economy / Mini',
                'description' => 'Hatchbacks & Small Cars',
                'icon' => 'bi-car-front',
                'capacity_seats' => 4,
                'capacity_bags' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Sedan',
                'description' => 'Comfortable Sedans',
                'icon' => 'bi-car-front-fill',
                'capacity_seats' => 4,
                'capacity_bags' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SUV / XL',
                'description' => 'For large groups',
                'icon' => 'bi-truck-front',
                'capacity_seats' => 6,
                'capacity_bags' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
