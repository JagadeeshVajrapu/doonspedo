<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    if (Schema::hasTable('vehicle_categories') && !Schema::hasColumn('vehicle_categories', 'branch_id')) {
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            // Assuming branch table is 'branches'
            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        echo "Added branch_id to vehicle_categories.\n";
    } else {
        echo "vehicle_categories already has branch_id or doesn't exist.\n";
    }

    if (Schema::hasTable('parcels') && !Schema::hasColumn('parcels', 'branch_id')) {
        Schema::table('parcels', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
        });
        echo "Added branch_id to parcels.\n";
    } else {
        echo "parcels already has branch_id or doesn't exist.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
