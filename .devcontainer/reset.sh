#!/bin/sh
set -e

cd /workspace

echo "Initializing Drupal Dev Container Demo."
echo "🗑️  Deleting logs, databases and temporary files ..."
rm -rf data
mkdir -p data/logs data/db data/files data/tmp

echo "🗃️  Recreating database ..."
echo 'DROP DATABASE IF EXISTS drupal; CREATE DATABASE drupal' | mariadb -h db --password=root

if [ -f /workspace/inbox/*sql ] 
then
    echo "🚚 Importing SQL dump ..."
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal
fi
if [ -f /workspace/inbox/*sql.gz ] 
then
    echo "📦 Importing gzipped SQL dump ..."
    zcat /workspace/inbox/*sql.gz | mariadb -h db -u root -proot drupal
fi

cd drupal
if [ -f /workspace/inbox/*code.tar* ]
then
    echo "📚 Importing code tarball ..."
    rm -rf   /workspace/drupal/*
    tar -xvf /workspace/inbox/*code.tar* --strip-components 1
    rm -rf   /workspace/drupal/.git

    cp -rf sites/default/modules/* /workspace/src/modules/  || true
    cp -rf sites/default/themes/* /workspace/src/themes/    || true

    composer install
else
    echo "💧 Composing a fresh copy of Drupal 10 ..."
    mkdir -p drupal

    rm -f ./composer.lock
    [ -f "/workspace/src/composer.json" ] && ln -fs /workspace/src/composer.json ./composer.json
    [ -f "/workspace/src/composer.lock" ] && ln -fs /workspace/src/composer.lock ./composer.lock
    composer install
fi

cd web/sites/default
echo "📁 Adding custom code directories ..."
[ -f "/workspace/src/modules" ]       && ln -fs /workspace/src/modules ./modules
[ -f "/workspace/src/themes" ]        && ln -fs /workspace/src/themes  ./themes

chown -R root:root /workspace/drupal/*

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/settings.local.php ./settings.php

cat << EOF

👉 http://localhost:8001 👈

EOF