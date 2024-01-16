<?php

$databases['default']['default'] = array (
  'driver' => 'mysql',
  'database' => 'drupal',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'isolation_level' => 'READ COMMITTED',
);

$settings['file_private_path'] = '/workspace/site/data/private';
$settings['file_public_path'] = 'sites/default/files';
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['skip_permissions_hardening'] = TRUE;
$settings['config_sync_directory'] = '/workspace/site/data/config';
$settings['hash_salt'] = getenv("HASH_SALT");
$settings['rebuild_access'] = FALSE;

$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^drupal$',
];