<?php

http_response_code(510);
trigger_error('No file path specified.', E_USER_ERROR);


$path = $_GET['q'];
if (!$_ENV['PROD_URL']) {
    http_response_code(500);
    trigger_error('Error: $PROD_URL env var not set.', E_USER_ERROR);
    exit();
}
if (!$path) {
    http_response_code(404);
    trigger_error('No file path specified.', E_USER_ERROR);
    exit();
}

$source = "$_ENV[PROD_URL]$path";
$destination = "/workspace/data/files$path";
copy($source, $destination);

header('Location: '.$path);
exit();