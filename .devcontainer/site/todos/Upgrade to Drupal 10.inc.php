<?php

todo("Create pre-upgrade reference", function() {
  todo("Install from current composer.json", function() {
    composer('install --ignore-platform-reqs');
  });
  
  todo("Get current module versions", function() {
    get_installed_packages("/workspace/site/outbox/composer-packages-before.txt", true);
  });

  todo("Get unpinned module versions", function() {
    get_installed_packages("/workspace/site/outbox/composer-packages-unpinned.txt", false);
    shh("cp -f /workspace/site/outbox/composer-packages-unpinned.txt /workspace/site/inbox/composer-packages-unpinned.txt");
  });
});

todo("Update custom code", function() {
  todo("Run Upgrade Status module", function() {
    return '!';
    composer('require drupal/upgrade_status');
    drush("en upgrade_status");
  });
});

todo("Check out a clean copy of composer.json", function() {
  chdir("/workspace/site/root/");
  shh('git reset .');
  shh('git remote add ic https://github.com/pantheon-upstreams/drupal-composer-managed.git && git fetch ic');
  shh('git checkout ic/main composer.json');
  shh('rm -rf /workspace/site/root/composer.lock');
});


todo("Upgrade Drupal core to 9.5", function() {
  // composer(
  //   'remove --no-update --no-audit ' .
  //   implode(' ', site_file("outbox/composer-packages-before.txt")) 
  // );
  composer('require --no-update --no-audit --ignore-platform-reqs drupal/core:^9.5 drupal/core-recommended:^9.5');
  composer('require --dev --no-update --no-audit --ignore-platform-reqs drupal/core-dev^9.5');
});



todo("Update contrib code", function() {
  todo("Re-add unpinned modules", function() {
    composer('config --no-plugins allow-plugins.phpstan/extension-installer true');
    composer('config --no-plugins allow-plugins.wikimedia/composer-merge-plugin true');
    composer('config --no-plugins allow-plugins.drupal/console-extend-plugin true');

    composer('config --no-plugins allow-plugins.drupal/console-extend-plugin true');
    composer('config minimum-stability dev');

    foreach (site_file("inbox/composer-packages-unpinned.txt") as $module) {
      if ($module) {
        composer("require --no-update --no-audit --ignore-platform-reqs $module");
      }
    }
  });

  todo("Upgrade Drupal core to 10", function() {
    composer(
      'remove --no-update --no-audit ' .
      implode(' ', site_file("outbox/composer-packages-before.txt")) 
    );
    composer('require --no-update --no-audit --ignore-platform-reqs drupal/core:^10 drupal/core-recommended:^10');
    composer('require --dev --no-update --no-audit --ignore-platform-reqs drupal/core-dev:^10');

    foreach (site_file("inbox/composer-packages-unpinned.txt") as $module) {
      if ($module) {
        composer("require --no-update --no-audit --ignore-platform-reqs $module");
      }
    }
  });

  todo("Fix, replace or remove incompatible modules", function() {
    todo("Get list of incompatible packages", function() {
      $out = composer('update --dry-run  --ignore-platform-reqs');
      $packages_needing_attention = "";
      foreach (explode("\n", $out) as $line) {
        if ($parts = get_matches('/^ +\- ([a-z0-9\-\_\/]+)\[(.+)\] require/', $line)) {    
          $packages_needing_attention .= "|**$parts[1]**||\n";
        }
      }

      if ($packages_needing_attention) {
        return <<<EOM
x

### Incompatible Modules

|Module|Use Version|
|-|-|
$packages_needing_attention

EOM;

      }
    });

    todo("Manually find compatible versions", function() {
      $done = true;
      foreach (todo_config('Incompatible Modules') as $package => $version) {
        $done = $done && !empty($version);
        todo("Find compatible version for **$package**", function() use ($version) {
          return $version ? "x" : ' ';
        });
      }
      return $done ? "x" : " ";
    });

    todo("Add resolved module versions", function() {
      todo_section('Incompatible Modules');
      return '!';
      foreach (site_config_section('Incompatible Modules') as $package => $version) {
        if ($version == 'remove') {
          todo ("Remove **$package**", function () use ($package) {
            composer("remove --no-update $package");
          });
        }
        else if (substr($version, 0, 1) == '#') {
          todo("Patch **$package** with **$version**", function() use ($package, $version) {
            $issue = substr($version, strlen('#'));
            $module = substr($package, strlen('drupal/'));
            $url = "https://git.drupalcode.org/issue/$module-$issue.git";
            composer("config repositories.$module git $url");
            // $branch = shh("git ls-remote $url | grep -o '$issue.*'");
            composer("require --no-update --no-audit --ignore-platform-reqs $package:dev-*");
          });
        }
        else if ($version) {
          todo("Update **$package** to **$version**", function() use ($package, $version) {
            composer("require --no-update --no-audit --ignore-platform-reqs  \"$package:$version\"");
          });
        }
      }
    });

  //   todo("Manually resolve incompatibilities", function() {
  //     $out = "";
  //     $missing = false;
  //     foreach (site_config_section('Incompatible Composer Packages') as $package => $version) {
  //       $out = "|**$package**|$version|";
  //       $missing = $missing || empty($version);
  //     }
  //     if ($out) {
  //       $out = !$missing ? 
  //               'x: done!' : 
  //               'i: Please add a composer version string or the word remove.' 
  //             . $out;
  //       $out .= <<<EOM
  
  // |Module|Version|
  // |-|-|
  // $out

  // ---
        
  // EOM;      
  //     }
  //     return $out;
  //   });

    todo("Install from updated composer.json", function() {
      shh('rm -rf /workspace/site/root/composer.lock');
      composer('update --ignore-platform-reqs');
      composer('install --ignore-platform-reqs');
    });
  });
});


/*
- [ ] Create pre-upgrade reference
  - [ ] Install from current composer.json
  - [ ] Get current module versions
- [ ] Upgrade Drupal
  - [ ] Update Contrib Code
    - [ ] Upgrade compatible modules
    - [ ] Apply known patches
    - [ ] Manually deal with incompatibilities
  - [ ] Update Custom Modules
    - [ ] Run rector
    - [ ] Manually deal with upgrade status findings
  - [ ] Update Custom Themes
    - [ ] Run automated update
    - [ ] Manually deal with upgrade status
- [ ] Update DB
- [ ] Manually Commit and push
*/