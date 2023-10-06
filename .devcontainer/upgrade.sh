#!/bin/bash
set -e

echo << EOM

💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧


     Starting upgrade from 💧9️⃣ to 💧🔟


💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧

EOM

# git switch --force-create d9-10-d10

echo "🔄 Reset upgrade destination ..."
cd /workspace/drupal10
git restore .

rm -rf vendor composer.lock

echo "🚚 Reset database ..."
cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10

echo "📄 Copy local settings ..."
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.php

echo "🖍️ Installing updated custom themes/modules"
cp -rf /workspace/src/themes/custom /workspace/drupal10/web/themes/
cp -rf /workspace/src/modules/custom /workspace/drupal10/web/modules/

echo "🩹 Update to latest 9.x"

echo "🪣 Remove repositories"
composer10 config --global discard-changes true
composer10 config --unset repositories
composer10 config --unset extra.
# composer10 config repositories.drupal composer https://packages.drupal.org/8

echo "🪡  Replace patches ..."
jq -s 'del(.[0].extra.patches)[0] * .[1]' /workspace/inbox/code/composer.json /workspace/inbox/patches.json > /workspace/drupal10/composer.json

#echo "❌ Drush disable rogue modules ..."
# drush10 pm:uninstall ds_devel

echo "🗑️ Remove outdated pantheon upstream ..." 
composer10 remove --no-update --no-audit "pantheon-upstreams/upstream-configuration"

echo "📌 Unpinning module versions ..."
cat /workspace/inbox/updatable-modules.txt | tr "\n" " " | xargs composer10 require --no-update --no-audit --ignore-platform-req=php

echo "📌 Pinning module versions ..."
composer10 require --no-update --no-audit --ignore-platform-req=php \
     "drupal/search_exclude_nid:^2.0@alpha" \
     "drupal/media_entity_browser:^2.0@alpha" \
     "drupal/config_update:^2.0@alpha" \
     "drupal/adminimal_theme:^1.7" \
     "drupal/color:^1.0" \
     "drupal/color_field:^3.0" \
     "drupal/select2boxes:^2.0@alpha" \
     "drupal/ief_table_view_mode:3.0.x-dev@dev" \
     "drupal/inline_entity_form:^1.0@RC" \
     "drupal/quickedit:^1.0" \
     "drupal/ds:^3.15" \
     "drupal/sliderwidget:2.x-dev@dev" \
     "drupal/scheduled_updates:1.x-dev@dev" \
     "drupal/fixed_text_link_formatter:1.x-dev@dev" \
     "drupal/adminimal_admin_toolbar:1.x-dev@dev" \
     "drupal/path_redirect_import:^2.0" \
     "drupal/core:^9.5" \
     "drupal/allowed_formats:^2.0" \
     "drupal/display_field_copy:2.x-dev@dev" \
     "drupal/linkit:^6.0" \
     "drupal/better_exposed_filters:6.0.1" \
     "drupal/jquery_ui_datepicker:^1.2" \
     "drush/drush:^11"

echo "🗑️ Composer remove rogue modules ..." 
# cat /workspace/inbox/unupdatable-modules.txt | tr "\n" " " | xargs composer10 remove --no-update --no-audit

composer10 remove --no-update --no-audit \
     drupal/console \
     drupal/console-extend-plugin \
     fzaninotto/faker \
     drupal/adminimal_admin_toolbar \
     drupal/adminimal_theme \
     drupal/better_exposed_filters


echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

# composer10 show --direct -f json | jq -r '.installed[] | "\(.name):\(.version)"' > /workspace/outbox/installed-2.txt

exit

echo "9️⃣ -> 🔟"
echo "🔟 Updating core to the latest 10.x version ..." 
composer10 require --no-update --ignore-platform-req=php 'drupal/core:^10'

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

# echo "📦 Composer bump ..." 
# composer10 bump

echo "📀 Drush update db ..." 
drush10 updb

drush10 uli admin/reports/status