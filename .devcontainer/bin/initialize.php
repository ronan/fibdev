#!/usr/local/bin/php
<?php
require_once "helpers.inc.php";

echo(<<< EOM


  ═╦════╗                         🌤️
   ║  [ | ]
___╩___      [  ][  ][  ]
\   🛟  |     [  ][  ][  ]  ________
 \     |_[  ][  ][  ][  ]_/ o o o /
  \______________________________/
🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊
🌊🌊🌊🌊 D9->D10 Migration Tool. 🌊🌊🌊🌊🌊🌊
🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊

EOM);

// if (empty($_ENV['SITE'])) {
//   if (exists("/workspace/site")) {
//     $target = readlink("/workspace/site");
//     $site_name = basename($target);
//     die("Site $site_name already initialized");
//   }
  
//   die("Please specify a 'SITE' environment variable");
// }
$site_name = isset($argv[1]) ? $argv[1] : site_name();
$code_repo = isset($argv[2]) ? $argv[2] : @$_ENV['SITE_REPO'];
$site_dir = site_directory($site_name);

if ($site_dir) {
  say("Current Site: $site_dir", "📣");
}
else {
  say("No Site Set. Run `initialize www.example.com [repourl]` to ingest or switch to a new site", "📣");
}

if (todo('Create site directory', "📂")) {
  // create_directory($site_dir, false);
  // create_directory("$site_dir/inbox", false);
  link_directory("$site_dir/data", "/workspace/data");
  link_directory($site_dir, "/workspace/site");
  $site_dir = site_directory($site_name);
  ok();
}

if (todo('Create TODO list', "✅")) {
  copy("/workspace/.devcontainer/todos/Upgrade to Drupal 10.md", "$site_dir/todo.md");
  ok();
}
todo_done('Test TODO list', 'x');

if (todo('Initialize environment', "🥚")) {
  foreach (['outbox', 'data/logs', 'data/tmp'] as $dir) {
    create_directory("$site_dir/$dir");
  }
  ok();
}

if (todo('Create default settings file', "🗂️")) {
  file_put_contents("$site_dir/info.ini", 
<<<EOM
SITE="$site_name"
PROD_URL="https://$site_name"
CODE_REPO="$code_repo"
EOM
  );
  ok();
}

if (todo('Delete logs, and temporary files', "🗑️")) {
  foreach (['outbox', 'data/logs', 'data/tmp'] as $dir) {
    create_directory("$site_dir/$dir");
  }
  ok();
}
