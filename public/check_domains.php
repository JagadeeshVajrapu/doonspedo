<?php
$dirs = glob('/home/u513571509/domains/*', GLOB_ONLYDIR);
if (empty($dirs)) {
    // maybe it's in a different root
    $dirs = glob(dirname(dirname(__DIR__)) . '/*'); 
}
echo json_encode($dirs);
