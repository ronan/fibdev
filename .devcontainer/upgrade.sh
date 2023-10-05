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

# echo "❌ Drush disable rogue modules ..."
# drush10 pm:uninstall \
#                     address                                 \
#                     ckeditor_bootstrap_grid                 \
#                     select2boxes                            \
#                     path_redirect_import                    \
#                     color                                   \
#                     quickedit                               \
#                     devel_entity_updates                    \
#                     display_field_copy                      \
#                     scheduled_updates                       \
#                     imce                                    \
#                     responsive_menu                         \
#                     adminimal_admin_toolbar                 \
#                     fixed_text_link_formatter

echo "🪡  Remove patches ..."
cat /workspace/inbox/code/composer.json | jq 'del(.. | .patches?)' > /workspace/drupal10/composer.json

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
cat /workspace/outbox/updatable-modules.txt | tr "\n" " " | xargs composer10 require --no-update --no-audit --ignore-platform-req=php

echo "🗑️ Composer remove rogue modules ..." 
cat /workspace/outbox/unupdatable-modules.txt | tr "\n" " " | xargs composer10 remove --no-update --no-audit

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

echo "📦 Composer bump ..." 
composer10 bump

echo "📀 Drush update db ..." 
drush10 updb

drush10 uli admin/reports/status