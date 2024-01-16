<?php

// http_response_code(510);
// trigger_error('No file path specified.', E_USER_ERROR);

// phpinfo();
$prod_url = "https://www.cidrap.umn.edu";
$path = preg_replace('/\?.*$/', "", $_SERVER['REQUEST_URI']);
if (!$prod_url) {
    http_response_code(500);
    trigger_error('Error: $PROD_URL env var not set.', E_USER_ERROR);
    exit();
}
if (!$path) {
    http_response_code(404);
    trigger_error('No file path specified.', E_USER_ERROR);
    exit();
}

$source = "$prod_url$path";
header("Location: $source");
die;

$destination = "/workspace/site/root/web" . urldecode($path);
if (!is_dir(dirname($destination))) {
    mkdir(dirname($destination), 0777, true);
}

copy($source, $destination);
echo "$source -> $destination";

// $fp = fopen($destination, 'rb');
// header("Content-Type: image/png");
// header("Content-Length: " . filesize($name));

// // dump the picture and stop the script
// fpassthru($fp);
