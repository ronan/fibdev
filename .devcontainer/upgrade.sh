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

echo "🚚 Reset database ..."
cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10

echo "📄 Copy local settings ..."
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.php

echo "🖍️ Installing updated custom themes/modules"
cp -rf /workspace/src/themes/custom /workspace/drupal10/web/themes/
cp -rf /workspace/src/modules/custom /workspace/drupal10/web/modules/

echo "❌ Drush disable rogue modules ..."
# drush10 pm:uninstall color
#git fetch "https://git.drupalcode.org/issue/display_field_copy-3287010.git" '3287010-automated-drupal-10'
#git checkout -b 'display_field_copy-3287010-3287010-automated-drupal-10' FETCH_HEAD
# cd /workspace/src/modules/custom
# git clone https://git.drupalcode.org/issue/fixed_text_link_formatter-3287603.git fixed_text_link_formatter
# git clone https://git.drupalcode.org/issue/display_field_copy-3287010.git display_field_copy
# git clone https://git.drupalcode.org/issue/sliderwidget-3157814.git sliderwidget
# git clone https://git.drupalcode.org/issue/scheduled_updates-3172330.git scheduled_updates

echo "🪡  Replace patches ..."
jq -s 'del(.[0].extra.patches)[0] * .[1]' /workspace/inbox/code/composer.json /workspace/inbox/patches.json > /workspace/drupal10/composer.json
# composer config --merge --json extra.drupal-lenient.allowed-list '["drupal/fixed_text_link_formatter"]'
# composer10 config --no-plugins allow-plugins.mglaman/composer-drupal-lenient true
# composer10 require --no-update --ignore-platform-req=php 'mglaman/composer-drupal-lenient'

echo "🪣 Remove repositories"
composer10 config --global discard-changes true
composer10 config --unset repositories
composer10 config repositories.drupal composer https://packages.drupal.org/8


echo "🗑️ Remove outdated pantheon upstream ..." 
composer10 remove --no-update --no-audit "pantheon-upstreams/upstream-configuration"

echo "⬆️ Updating core to the latest 10.x version ..." 
composer10 require --no-update --ignore-platform-req=php 'drupal/core:^10'

echo "📌 Unpinning module versions ..."
# composer10 show --direct -f json | jq -r '.installed[] | "\(.name)"' > /workspace/outbox/updatable-modules.txt
cat /workspace/inbox/updatable-modules.txt | tr "\n" " " | xargs composer10 require --no-update --no-audit --ignore-platform-req=php

echo "🗑️ Composer remove rogue modules ..." 
cat /workspace/inbox/unupdatable-modules.txt | tr "\n" " " | xargs composer10 remove --no-update --no-audit

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

# echo "📦 Composer bump ..." 
# composer10 bump

echo "📀 Drush update db ..." 
drush10 updb

drush10 uli admin/reports/status