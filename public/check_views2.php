<?php
$dir = __DIR__.'/../resources/views/admin/drivers/';
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "Files in admin/drivers:\n";
    print_r($files);
} else {
    echo "Directory does not exist: $dir";
}
