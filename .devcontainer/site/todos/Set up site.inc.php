<?php

todo("Configure the site");
todo('Create site directory', function() {
  create_directory($GLOBALS['site_directory'], false);
  if (!file_exists("/workspace/site/README.md")) {
    copy("/workspace/.devcontainer/site/README.md", "/workspace/site/README.md");
  }
  todo('Test TODO list', function() {});
  todo('(Re)Create logs, and temporary directories', function() {
    foreach (['outbox', 'data/logs', 'data/tmp'] as $dir) {
      create_directory("/workspace/site/$dir");
    }
  });
});



todo("Create the code root", function() {  
  if (is_dir("/workspace/site/inbox/code")) {
    link_directory("/workspace/site/inbox/code", "/workspace/site/root");
    return;
  }
  if (empty($_ENV['CODE_REPO'])) {
    todo('Specify the git repo url', function () {
      return; 
    });
  }

  todo('Clone the code from git', function() {
  });

  todo("Install with composer", function() {
    // composer('require --no-update --n drush/drush');
    composer('install');
  });

  todo("Add settings.local.php override file", function() {
    copy("/workspace/.devcontainer/drupal/settings.local.php", "/workspace/site/root/web/sites/default/settings.local.php");
  });
});

todo("Enable production file proxy", function() {
  if (!file_exists('/workspace/site/root/web/proxyfile.php')) {
    symlink(
      '/workspace/.devcontainer/bin/proxyfile.php', 
      '/workspace/site/root/web/proxyfile.php'
    );
  }
});

todo("Set up the database", function() {
  todo("Delete and recreate the database", function() {
    sh("mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal; CREATE DATABASE drupal'");
  });

  todo("Import the production data", function() {
    $sql_files = glob("/workspace/site/inbox/*.sql*");
    $import_file = array_pop($sql_files);
    if (!$import_file) {
      todo_uncheck("Get data from prod");
      err("Please place a sql file in /workspace/site/inbox");
    }
    $cat = get_matches('/\.gz(ip)?$/', $import_file) ? 'zcat' : 'cat';
    sh("$cat $import_file | mariadb -h db -u root -proot drupal");
    todo_check('Specify the git repo url');
  });
});

todo("Create VRT Reference");