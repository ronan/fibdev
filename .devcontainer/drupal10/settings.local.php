<?php
assert_options(ASSERT_ACTIVE, TRUE);
assert_options(ASSERT_EXCEPTION, TRUE);

$databases['default']['default'] = array (
  'database' => 'drupal10',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'isolation_level' => 'READ COMMITTED',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
);

$settings['file_private_path'] = '/workspace/data/private';
$settings['file_public_path'] = 'sites/default/files';
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['skip_permissions_hardening'] = TRUE;
$settings['config_sync_directory'] = '/workspace/data/config';
$settings['hash_salt'] = getenv("HASH_SALT");
$settings['rebuild_access'] = FALSE;

$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^drupal10$',
  '^drupal10\.local$'
];