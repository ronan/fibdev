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


$site_dir = site_directory();
chdir("$site_dir/root");

start("Reset upgrade destination", '🔄');
shh('git restore .');
shh('git clean -f -x');
ok();


start("Getting installed modules", '🧩');
$packages = array();
if (file_exists("$site_dir/inbox/composer-packages.txt")) {
    $packages = explode("\n", file_get_contents("$site_dir/inbox/composer-packages.txt"));
}
else {
    if (!file_exists("$site_dir/outbox/composer-packages-before.txt")) {
        ssh('composer install');
        get_installed_packages("$site_dir/outbox/composer-packages-before.txt");
    }
    foreach(file("$site_dir/outbox/installed-before.txt") as $line) {
        $parts = explode(':', $line);
        $packages[] = $parts[0];
    }
}
ok();

// If pantheon but not 'pantheon-upstreams/drupal-composer-managed'
// 
if (false) {
    # https://docs.pantheon.io/guides/drupal-hosted-createcustom/new-branch
    start("Switching to managed composer", '🧼');
    ssh('git remote add ic https://github.com/pantheon-upstreams/drupal-composer-managed.git && git fetch ic');
    ssh('git checkout -b backdev-upgrade');
    # Todo: Move the custom modules out of the way first
    if (!is_dir("$site_dir/custom")) {
        create_directory("$site_dir/custom", false);
        copy("$site/web/modules/custom", "$site_dir/custom/modules");
        copy("$site/web/themes/custom", "$site_dir/custom/themes");
    }
    ssh('git rm -rf ./*');
    ssh('git checkout ic/main .');
    # TODO: verify the gitignore is right.
}


// $patches = [
//     ['https://git.drupalcode.org/issue/fixed_text_link_formatter-3287603.git', '3287603-automated-drupal-10'],
//     ['https://git.drupalcode.org/issue/image_field_caption-3355337.git', 'dev-3355337-support-drupal-10'],    
// ];


start("Upgrade core", '⬆️');
unlink("$site_dir/root/composer.lock");
shh(
        "composer remove --no-update --no-audit " . 
        strtr(file_get_contents("$site_dir/outbox/installed-before.txt"), "\n", " ")
    );
shh('composer require --ignore-platform-reqs drupal/core:^10.1 drupal/core-recommended:^10.1 ');
shh('composer require --no-update --no-audit --ignore-platform-reqs ' . implode(" ", $packages));
ok();

start("Checking for install problems", '🤕');
$problems = shh('composer install --dry-run 2>&1 | grep Problem -A 2 | grep -v Problem');
file_put_contents("$site_dir/outbox/composer-upgrade-problems.txt", $problems);
if ($problems) {
    err("Install problems found: $problems");
    exit;
}
ok();

start("Installing. Please insert disk 12 of 47", '💾');
shh('composer install --ignore-platform-reqs --no-audit');
get_installed_packages("$site_dir/outbox/composer-packages-after.txt");
ok();
