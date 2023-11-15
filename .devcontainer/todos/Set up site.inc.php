<?php

todo('Create site directory', function() {
  create_directory($GLOBALS['site_directory'], false);
  todo_check('Create README.md');
});

todo('Create README.md', function() {
  copy("/workspace/.devcontainer/todos/README.md", "/workspace/site/README.md");
  todo_check('Create README.md');
});

todo('Test TODO list', function() {});

todo('Initialize environment', function() {
  todo_uncheck('Delete logs, and temporary files');
});

todo('Delete logs, and temporary files', function() {
  foreach (['outbox', 'data/logs', 'data/tmp'] as $dir) {
    create_directory("/workspace/site/$dir");
  }
});

todo("Create the code root", function() {
  if (is_dir("/workspace/site/inbox/code")) {
    link_directory("/workspace/site/inbox/code", "/workspace/site/root");
    todo_state('Specify the git repo url', '-');
    todo_state('Clone the code from git', '-');
  }
  else if (!empty($_ENV['CODE_REPO'])) {
    todo_check('Specify the git repo url');
    todo_uncheck('Clone the code from git');
  }
  else {
    err(<<<EOM
Could not initialize a code root. Please do one of the following:

  Add a repository url to /workspace/site/info.ini
      or 
  Place a copy of the code at /workspace/site/inbox/code
EOM);
  }
});

todo('Clone the code from git', function() {
  $repo = config('Git Repository') || err("No code repository specified.");
  system("git clone $repo /workspace/site/root");
});

todo("Add settings.local.php override file", function() {
  copy("/workspace/.devcontainer/drupal/settings.local.php", "/workspace/site/root/web/sites/default/settings.local.php");
});

todo("Set up the database", function() {});

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