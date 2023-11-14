<?php

todo("Create pre-upgrade reference", "", function() {
  // Do nothing
});


todo("Install from current composer.json", "", function() {
  composer('install');
});

todo("Get current module versions", "", function() {
  get_installed_packages("/workspace/site/outbox/composer-packages-before.txt", true);
});

todo("Update contrib code using Composer", "", function() {});

todo("Get unpinned module versions", "", function() {
  get_installed_packages("/workspace/site/outbox/composer-packages-unpinned.txt", false);
});

todo("Remove all modules", "", function() {
  composer(
    'remove --no-update --no-audit --ignore-platform-reqs ' . 
    implode(' ', site_file("outbox/composer-packages-unpinned.txt")) 
  );
});

todo("Upgrade Drupal core", "", function() {
  if (file_exists("/workspace/site/root/composer.lock")) {
    unlink("/workspace/site/root/composer.lock");
  }
  composer(
    'remove --no-update --no-audit ' .
    implode(' ', site_file("outbox/composer-packages-before.txt")) 
  );
  composer('require --no-update --no-audit --ignore-platform-reqs drupal/core:^10.1 drupal/core-recommended:^10.1');
  composer('require-dev --no-update --no-audit --ignore-platform-reqs drupal/core-dev:^10.1');
});

todo("Re-add unpinned modules", "", function() {
  composer(
      'require --no-update --no-audit --ignore-platform-reqs ' . 
      implode(' ', site_file("outbox/composer-packages-unpinned.txt")) 
    );
});

todo("Get list of composer install problems", "", function() {
  $out = composer('install --dry-run  --ignore-platform-reqs');

  $problems = shh("echo \"$out\" | grep Problem -A 2 | grep -v Problem");
  site_file("outbox/composer-upgrade-problems.txt", array($problems));
});

todo("Add patched modules", "", function() {
  $patches = array(
    '3355337' => 'image_field_caption'
  );
  foreach ($patches as $id => $module) {
    $url = "https://git.drupalcode.org/issue/$module-$id.git";
    composer("config repositories.$module git $url");
    $branch = shh("git ls-remote $url | grep -o '$id.*'");
    composer("require --no-update --no-audit --ignore-platform-reqs drupal/$module:dev-$branch");
  }
});

todo("Install from updated composer.json", "", function() {
  composer('install  --ignore-platform-reqs');
});