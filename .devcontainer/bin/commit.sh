#!/bin/bash

echo "🔗 Hydrating themes/modules"
rm -rf /workspace/drupal10/web/themes/custom
rm -rf /workspace/drupal10/web/modules/custom
cp -r /workspace/src/themes /workspace/drupal10/web/themes/custom
cp -r /workspace/src/modules /workspace/drupal10/web/modules/custom

cd web/themes/custom/savethebw_theme/
yarn build
