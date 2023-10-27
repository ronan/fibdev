#!/bin/bash
set -e

cat << EOM
                                   🌤️
    ═╦════╗
     ║  [ | ]
  ___╩___      [ | ][ | ][ | ]
  \   🛟  |     [ 9️⃣ ][ ⇨ ][ 🔟]  _________
   \     |_[| ][ | ][ | ][ | ]_/ o o o /
    \__________________________________/
💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧
EOM


cd /workspace/drupal10

echo "🔄 Reset upgrade destination ..."
git restore .
git clean -f -x

echo "📚 Add Required libraries ..."
composer10 config --no-plugins allow-plugins.phpstan/extension-installer true
composer10 config --no-plugins allow-plugins.wikimedia/composer-merge-plugin true
composer10 config --json extra.merge-plugin '{"include":["web/modules/contrib/webform/composer.libraries.json","web/modules/custom/savethebw/composer.libraries.json"]}'

composer10 config minimum-stability dev
composer10 config --unset repositories
composer10 config repositories.pantheon_upstream path upstream-configuration
composer10 config repositories.drupal composer https://packages.drupal.org/8
composer10 config repositories.fixed_text_link_formatter git https://git.drupalcode.org/issue/fixed_text_link_formatter-3287603.git
composer10 config repositories.sliderwidget git https://git.drupalcode.org/issue/sliderwidget-3157814.git
composer10 config repositories.path_redirect_import git https://git.drupalcode.org/issue/path_redirect_import-3373025.git

echo "⬆️  Upgrade core ..."
composer10 require --no-update --no-audit --ignore-platform-reqs \
     drupal/core:^10.1 \
     drupal/core-recommended:^10.1

echo "⬆️  Upgrade core-dev ..."
composer10 require --dev --no-update --no-audit --ignore-platform-reqs \
     drupal/core-dev:^10.1

echo "📌 Unpin module versions ..."
composer10 require --no-update --no-audit --ignore-platform-reqs \
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
     drupal/display_field_copy \
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
     drupal/multiple_fields_remove_button:^2.2 \
     drupal/name \
     drupal/paragraphs \
     drupal/paragraphs_limits \
     drupal/pathauto \
     drupal/path_redirect_import:dev-3373025-update-to-support \
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
     drupal/svg_image \
     drupal/token \
     drupal/token_filter \
     drupal/twig_tweak \
     drupal/video:^3.0 \
     drupal/views_ajax_history \
     drupal/views_block_filter_block \
     drupal/views_bootstrap \
     drupal/views_custom_cache_tag \
     drupal/views_field_formatter \
     drupal/views_infinite_scroll \
     drupal/viewsreference \
     drupal/webform \
     drupal/youtube \
     wikimedia/composer-merge-plugin \
     drupal/adminimal_admin_toolbar:1.x-dev@dev \
     drupal/adminimal_theme:^1.7 \
     drupal/allowed_formats:^2.0 \
     drupal/better_exposed_filters:^6.0.3 \
     drupal/color_field:^3.0 \
     drupal/color:^1.0 \
     drupal/config_update:^2.0@alpha \
     drupal/ds:^3.15 \
     drupal/ief_table_view_mode:3.0.x-dev@dev \
     drupal/inline_entity_form:^1.0@RC \
     drupal/jquery_ui_datepicker:^2.0 \
     drupal/jquery_ui_slider:^2.0 \
     drupal/linkit:^6.0 \
     drupal/media_entity_browser:^2.0@alpha \
     drupal/quickedit:^1.0 \
     drupal/scheduled_updates:2.x-dev@dev \
     drupal/search_exclude_nid:^2.0@alpha \
     drupal/select2boxes:^2.0@alpha \
     drupal/smart_date:^4.0 \
     drupal/sliderwidget:dev-3157814-drupal-10-support \
     drupal/fixed_text_link_formatter:dev-3287603-automated-drupal-10

echo "💾 Composer update ..."
composer10 update --no-install --with-all-dependencies --ignore-platform-reqs

echo "💿 Composer install ..."
composer10 install --ignore-platform-reqs

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.local.php

# cp -r /workspace/src/modules/* /workspace/drupal10/web/modules/custom/
# cp -r /workspace/src/themes/* /workspace/drupal10/web/themes/custom/

drush10 updb
drush10 cr
drush10 uli admin/reports/status
