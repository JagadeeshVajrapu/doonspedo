<?php
$dir = __DIR__.'/../resources/views/branch/drivers/';
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "Files in branch/drivers:\n";
    print_r($files);
} else {
    echo "Directory does not exist: $dir";
}
