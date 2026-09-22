<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Parcel;

try {
    // 1. Light Commercial (Mini Truck)
    Parcel::firstOrCreate(
        ['name' => 'Light Commercial (Mini Truck)'],
        [
            'description' => 'Ideal for moving small furniture or multiple boxes.',
            'icon' => 'bi-truck',
            'max_load' => '750 KG',
            'base_price' => 25.00,
            'price_per_km' => 2.50,
            'is_active' => 1
        ]
    );

    // 2. Heavy Duty Truck
    Parcel::firstOrCreate(
        ['name' => 'Heavy Duty Truck'],
        [
            'description' => 'For industrial goods, large shifting, or heavy cargo.',
            'icon' => 'bi-truck-flatbed',
            'max_load' => '3000 KG',
            'base_price' => 75.00,
            'price_per_km' => 5.00,
            'is_active' => 1
        ]
    );

    // 3. 2-Wheeler Courier
    Parcel::firstOrCreate(
        ['name' => '2-Wheeler Courier'],
        [
            'description' => 'Fast delivery for documents and small packages.',
            'icon' => 'bi-box-seam',
            'max_load' => '20 KG',
            'base_price' => 10.00,
            'price_per_km' => 0.80,
            'is_active' => 1
        ]
    );

    echo "Initial static parcel categories inserted into database successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
