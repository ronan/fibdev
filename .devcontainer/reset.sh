#!/bin/sh
set -e

cd /workspace

echo "Initializing Backdrop Dev Container Demo."
echo "🗑️  Deleting logs, databases and temporary files ..."
rm -rf data
mkdir -p data/logs data/db data/files data/tmp

echo "🗃️  Recreating database ..."
echo 'DROP DATABASE IF EXISTS backdrop; CREATE DATABASE backdrop' | mariadb -h db --password=root

if [ -f /workspace/inbox/*sql ] 
then
    echo "🚚 Importing SQL dump ..."
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot backdrop
fi
if [ -f /workspace/inbox/*sql.gz ] 
then
    echo "📦 Importing gzipped SQL dump ..."
    zcat /workspace/inbox/*sql.gz | mariadb -h db -u root -proot backdrop
fi

if [ -f /workspace/inbox/*code.tar* ]
then
    echo "📚 Importing a Backup ..."
    echo "🛑 J/k this isn't done yet."
    exit -1
else
    echo "🐉 Unzipping a fresh copy of Backdrop ..."
    mkdir -p backdrop
    tar -xz --strip-components=1 -C /workspace/backdrop -f /backdrop.tar.gz
    rm -rf /workspace/backdrop/modules  /workspace/src/themes
fi

echo "📁 Adding custom code directories ..."
[ -f "/workspace/src/modules" ]  && ln -fs /workspace/src/modules /workspace/backdrop/modules
[ -f "/workspace/src/themes" ]   && ln -fs /workspace/src/themes  /workspace/backdrop/themes

chown -R root:root /workspace/backdrop/*

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/settings.local.php /workspace/backdrop/settings.local.php

# echo "🧩 Installing developer modules ..."
# TODO: Bee is not working yet
# bee dl backup_migrate
# bee en backup_migrate
# bee dl devel
# bee en devel

cat << EOF

👉 http://localhost:8001 👈

EOF