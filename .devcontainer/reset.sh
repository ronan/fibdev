#!/bin/sh
set -e

cd /workspace

echo "Initializing Drupal Dev Container Demo."
echo "🗑️  Deleting files ..."
rm -rf data
mkdir -p data/logs data/db data/files data/tmp

echo "🗃️  Dropping database ..."
echo 'DROP DATABASE IF EXISTS drupal' | mariadb -h db --password=root

echo "💧 Installing a fresh Drupal ..."
mkdir -p drupal

cd drupal
rm -f ./composer.lock
[ -f "/workspace/src/composer.json" ] && ln -fs /workspace/src/composer.json ./composer.json
[ -f "/workspace/src/composer.lock" ] && ln -fs /workspace/src/composer.lock ./composer.lock
composer install

cd web/sites/default
echo "📁 Adding custom code directories ..."
[ -f "/workspace/src/modules" ]       && ln -fs /workspace/src/modules ./modules
[ -f "/workspace/src/themes" ]        && ln -fs /workspace/src/themes  ./themes

echo "📝 Overwriting settings ..."
[ -f "/workspace/src/settings.php" ]  && ln -fs /workspace/src/modules ./settings.overrides.php
cp -f /workspace/.devcontainer/settings.php ./settings.php

cat << EOF


👉 http://localhost:8001 👈


EOF