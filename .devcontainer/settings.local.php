<?php
$database = 'mysql://root:root@db/backdrop';


$settings['trusted_host_patterns'] = array(
  '^localhost\:8001$',
);

$config_directories['active']  = '/workspace/data/config/active';
$config_directories['staging'] = '/workspace/data/config/staging';

$settings['file_private_path'] = '/workspace/data/files/private';
$settings['file_public_path']  = '/workspace/data/files/public';

$settings['hash_salt'] = getenv("HASH_SALT");
// $settings['backdrop_drupal_compatibility'] = TRUE;
