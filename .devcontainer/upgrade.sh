#!/bin/bash
set -e

echo "🔄 Reset upgrade destination ..."
cd /workspace/drupal10
git restore .

echo "🚚 Reset database ..."
cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10

echo "📄 Copy local settings ..."
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.php

# rm -rf vendor web/modules/composer composer.lock

echo "🎼 Composer install ..."
composer10 install

echo "🪡  Remove patches ..."
cat /workspace/inbox/code/composer.json | jq 'del(.. | .patches?)' > /workspace/drupal10/composer.json

composer10 config --global discard-changes true

# echo "🪣 Remove repositories"
# composer10 config repositories.x vcs https://github.com/foo/bar
# composer10 config --unset repositories.0
# composer10 config --unset repositories.1
# composer10 config --unset repositories.x


# echo "🛠️  Add latest drush ..."
# composer10 require drush/drush
echo "🧰 Add developer modules ..."
composer10 require --no-install --no-audit --ignore-platform-req=php drush/drush


# echo "❌ Disable rogue modules ..."
drush10 pm:uninstall address \
                     ckeditor_bootstrap_grid \
                     ckeditor_bootstrap_grid \
                     select2boxes \
                     sliderwidget \
                     path_redirect_import \
                     color \
                     quickedit \
                     devel_entity_updates \
                     display_field_copy \
                     scheduled_updates \
                     imce

echo "🗑️ Remove rogue modules ..."
composer10 remove --no-audit \
                    "drupal/address" \
                    "drupal/ckeditor_bootstrap_grid" \
                    "drupal/select2boxes" \
                    "drupal/sliderwidget" \
                    "drupal/path_redirect_import" \
                    "drupal/devel_entity_updates" \
                    "drupal/display_field_copy" \
                    "drupal/scheduled_updates" \
                    "drupal/imce"


echo "📌 Unpinning module versions ..." 
modules=`composer10 outdated --direct "drupal/*" -f json | jq -r '.installed[] | "\(.name)"' | tr "\n" " "`
composer10 require --no-install --no-audit --with-all-dependencies --ignore-platform-req=php $modules


echo "⬆️  Updating core and modules to lastest 9.x version ..."
composer10 update --with-all-dependencies --ignore-platform-req=php
drush10 updb

# echo "📦 Composer update ..." 
# composer10 update --no-install --no-audit
# echo "📦 Composer bump ..." 
# composer10 bump
# echo "📦 Composer install ..." 
# composer10 install

# echo "🔍 Running upgrade status ..."
composer10 require --no-audit --ignore-platform-req=php --dev drupal/upgrade_status
drush10 pm:enable upgrade_status
# drush10 us-a --all  --ignore-custom --ignore-uninstalled > /workspace/outbox/upgrade-status.txt

drush10 uli
exit

echo "⬆️ Updating core to the latest 10.x version" 
composer10 require --ignore-platform-req=php --update-with-dependencies "drupal/core-recommended:^10"

echo "🧩 Updating modules to the latest 10.x version" 
composer10 require --ignore-platform-req=php --update-with-dependencies "drupal/*"

# echo "🔍 Running upgrade status"
# drush10 en upgrade_status
# drush10 us-a --all  --ignore-custom --ignore-uninstalled > /workspace/outbox/upgrade-status.txt


# echo "🧱 Composer install ..."
# yes | composer9 install --no-interaction
