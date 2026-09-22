<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Start routes:\n";
$routes = app('router')->getRoutes();
$count = count($routes);
echo "Total routes: $count\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'vehicle') !== false || strpos($route->uri(), 'freight') !== false) {
        echo $route->uri() . "\n";
    }
}
echo "\nEnd routes";
