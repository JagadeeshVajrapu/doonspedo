<?php
$dir = __DIR__.'/../resources/views/admin/vehicles/';
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "Files in admin/vehicles:\n";
    print_r($files);
} else {
    echo "Directory does not exist: $dir";
}
