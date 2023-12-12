#!/usr/local/bin/php
<?php
require_once "helpers.inc.php";

$site_name = isset($argv[1]) ? $argv[1] : $_ENV['SITE'];
$code_repo = isset($argv[2]) ? $argv[2] : @$_ENV['SITE_REPO'];
$site_dir = site_directory($site_name);


start("Initializing $site_dir", "📂");
create_directory($site_dir);
if (!file_exists("$site_dir/info.ini")) {
  file_put_contents("$site_dir/info.ini", 
<<<EOM
SITE="$site_name"
PROD_URL="https://$site_name"
CODE_REPO="$code_repo"
EOM
  );
}
foreach (['outbox', 'data/logs', 'data/tmp', 'data/wget'] as $dir) {
  create_directory("$site_dir/$dir");
}
create_directory("$site_dir/inbox", false);
start("Deleting logs, databases and temporary files", "🗑️");
ok();

start("Making $site_dir the current default", "📂");
link_directory("$site_dir/data", "/workspace/data");
link_directory("$site_dir", "/workspace/site");
ok();

start("Creating the app root from code", "📂");
if (!is_dir("$site_dir/root")) {
  if (is_dir("$site_dir/inbox/code")) {
    link_directory("$site_dir/inbox/code", "$site_dir/root");
  }
  else if ($code_repo) {
    system("git clone $code_repo $site_dir/root");
  }
}
if (!is_dir("$site_dir/root")) {
  err("Could not initialize a code root. Add a repository url to $site_dir/.env, call `ingest $site_name [repository]` or place the code at $site_dir/inbox/code");
  exit();
}
ok();


if (file_exists("$site_dir/root/.ddev")) {
  system("ddev pull platform --environment=PLATFORMSH_CLI_TOKEN=$_ENV[PLATFORMSH_CLI_TOKEN],PLATFORM_PROJECT=$_ENV[PLATFORM_PROJECT],PLATFORM_ENVIRONMENT=$_ENV[PLATFORM_ENVIRONMENT]");
  if (file_exists("$site_dir/inbox/settings.local.php")) {
    copy("$site_dir/inbox/settings.local.php", "$site_dir/root/web/sites/default/settings.local.php");
  }
}
else {
  start("Adding local settings", "📝");
  copy("/workspace/.devcontainer/drupal/settings.local.php", "$site_dir/root/web/sites/default/settings.local.php");
  ok();

  start("Composer install", "💾");
  system("/workspace/.devcontainer/drupal/composer.sh install --no-interaction --ignore-platform-reqs");
  ok();  

  $db = glob("$site_dir/inbox/*.sql*");
  if (empty($db)) {
      err("Please place a sql file in $site_dir/inbox");
      die;
  }
  $dump_file = array_pop($db);
  start("Importing database from $dump_file", "💽");
  system("mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal; CREATE DATABASE drupal'");
  $cat = "cat";
  if (
      str_ends_with($dump_file, ".gz")     || 
      str_ends_with($dump_file, ".gzip")
      ) {
        $cat = "zcat";
  }
  system("$cat $dump_file | mariadb -h db -u root -proot drupal");
  ok();

  $files_dir = "$site_dir/inbox/files";
  if (is_dir($files_dir)) {
      start("Importing files from $files_dir", "📂");
      symlink_directory($files_dir, "$site_dir/root/web/sites/default/files");
      ok();
  }
}

