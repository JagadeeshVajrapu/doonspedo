<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$columns = DB::select('DESCRIBE driver_registrations');
foreach($columns as $col) {
    echo $col->Field . " | " . $col->Type . " | Null: " . $col->Null . " | Default: " . $col->Default . "\n";
}
