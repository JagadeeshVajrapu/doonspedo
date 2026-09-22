<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
use Illuminate\Support\Facades\DB;
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $prop = 'Tables_in_' . env('DB_DATABASE');
    if (isset($table->$prop)) {
        echo $table->$prop . "\n";
    } else {
        $vars = get_object_vars($table);
        echo reset($vars) . "\n";
    }
}
