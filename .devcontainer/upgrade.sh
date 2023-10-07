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

echo "🧼 Clean old composer stuff ..."
rm -rf vendor composer.lock web/modules/composer

echo "🚚 Reset database ..."
cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10

echo "📄 Copy local settings ..."
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.php

echo "🖍️ Installing updated custom themes/modules"
echo "🔗 Symlinking custom themes/modules"
rm -rf /workspace/drupal10/web/themes/custom
rm -rf /workspace/drupal10/web/modules/custom
ln -s /workspace/src/themes /workspace/drupal10/web/themes/custom
ln -s /workspace/src/modules /workspace/drupal10/web/modules/custom

echo "🩹 Update to latest 9.x"

echo "🪣 Remove repositories"
composer10 config --global discard-changes true
composer10 config --unset repositories
composer10 config --unset extra.
# composer10 config repositories.drupal composer https://packages.drupal.org/8

echo "🪡  Replace patches ..."
jq -s 'del(.[0].extra.patches)[0] * .[1]' /workspace/inbox/code/composer.json /workspace/inbox/patches.json > /workspace/drupal10/composer.json

echo "🗑️ Remove outdated pantheon upstream ..." 
composer10 remove --no-update --no-audit "pantheon-upstreams/upstream-configuration"

echo "⛙ Add merge plugin for webform"
composer10 require --no-update --no-audit --ignore-platform-req=php wikimedia/composer-merge-plugin
composer10 config --no-plugins allow-plugins.wikimedia/composer-merge-plugin true

echo "🗑️ Remove obsolete and patched modules ..." 
composer10 remove --no-update --no-audit \
     drupal/console \
     drupal/console-extend-plugin \
     drupal/display_field_copy \
     drupal/fixed_text_link_formatter \
     drupal/slider_widget \
     drupal/scheduled_updates \
     drupal/path_redirect_import \
     fzaninotto/faker \
     gajus/dindent \
     kint-php/kint

echo "📌 Unpin module versions ..."
# cat /workspace/inbox/updatable-modules.txt | tr "\n" " " | xargs composer10 require --no-update --no-audit --ignore-platform-req=php
composer10 require --no-update --no-audit --ignore-platform-req=php \
     composer/installers \
     drupal/address \
     drupal/admin_toolbar \
     drupal/auto_entitylabel \
     drupal/better_social_sharing_buttons \
     drupal/block_content_permissions \
     drupal/bootstrap_barrio \
     drupal/ckeditor_bootstrap_grid \
     drupal/crazyegg \
     drupal/crop \
     drupal/ctools \
     drupal/dblog_filter \
     drupal/devel \
     drupal/devel_entity_updates \
     drupal/editor_file \
     drupal/entity_browser \
     drupal/entity_usage \
     drupal/extlink \
     drupal/field_group \
     drupal/field_group_link \
     drupal/google_analytics \
     drupal/honeypot \
     drupal/image_effects \
     drupal/image_widget_crop \
     drupal/imce \
     drupal/link_plain_text_formatter \
     drupal/linked_field \
     drupal/menu_item_extras \
     drupal/metatag \
     drupal/name \
     drupal/paragraphs \
     drupal/paragraphs_limits \
     drupal/pathauto \
     drupal/protected_pages \
     drupal/quick_node_clone \
     drupal/r4032login \
     drupal/rabbit_hole \
     drupal/redirect \
     drupal/responsive_menu \
     drupal/migrate_tools \
     drupal/migrate_source_csv \
     drupal/sendgrid_integration \
     drupal/slick_views \
     drupal/smart_date \
     drupal/svg_image \
     drupal/token \
     drupal/token_filter \
     drupal/twig_tweak \
     drupal/video \
     drupal/views_ajax_history \
     drupal/views_block_filter_block \
     drupal/views_bootstrap \
     drupal/views_custom_cache_tag \
     drupal/views_field_formatter \
     drupal/views_infinite_scroll \
     drupal/viewsreference \
     drupal/webform \
     drupal/youtube

echo "📌 Pin module versions ..."
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
     "drupal/adminimal_admin_toolbar:1.x-dev@dev" \
     "drupal/core:^9.5" \
     "drupal/allowed_formats:^2.0" \
     "drupal/linkit:^6.0" \
     "drush/drush:^11" \
     "drupal/better_exposed_filters:^6.0.3" \
     "drupal/jquery_ui_slider:^2.0" \
     "drupal/jquery_ui_datepicker:^2.0"

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

# composer10 show --direct -f json | jq -r '.installed[] | "\(.name):\(.version)"' > /workspace/outbox/installed-2.txt

echo "9️⃣ -> 🔟"
echo "🔟 Updating core to the latest 10.x version ..." 
composer10 require --no-update --ignore-platform-req=php 'drupal/core:^10'

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php

echo "📦 Composer install ..." 
composer10 install --ignore-platform-req=php

# echo "📦 Composer bump ..." 
# composer10 bump

# echo "❌ Drush disable rogue modules ..."
# drush10 pm:uninstall ckeditor
# drush10 pm:enable ckeditor5


echo "📀 Drush update db ..." 
drush10 updb


drush10 uli admin/reports/status
