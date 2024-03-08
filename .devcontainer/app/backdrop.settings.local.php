<?php

$database = 'mysql://root:root@db/app';

// $config_directories['active'] = '/workspace/inbox/config/active';
// $config_directories['staging'] = '/workspace/inbox/config/staging';

// Do not remove config files from staging when they're imported.
$config['system.core']['config_sync_clear_staging'] = 0;
$settings['update_free_access'] = TRUE;
$settings['hash_salt'] = getenv("HASH_SALT");
$settings['backdrop_drupal_compatibility'] = FALSE;


// $settings['file_private_path'] = '/workspace/site/data/private';
// $settings['file_public_path'] = 'sites/default/files';
// $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
// $settings['skip_permissions_hardening'] = TRUE;
// $settings['config_sync_directory'] = '/workspace/site/data/config';
// $settings['rebuild_access'] = FALSE;

// $config['system.logging']['error_level'] = 'verbose';
// $config['system.performance']['css']['preprocess'] = FALSE;
// $config['system.performance']['js']['preprocess'] = FALSE;

// $settings['trusted_host_patterns'] = [
//   '^localhost$',
//   '^drupal$',
// ];