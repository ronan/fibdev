#!/bin/bash
set -e

cd /workspace

echo "Initializing Backdrop Dev Container Demo."
echo "🗑️  Deleting logs, databases and temporary files ..."
rm -rf data
mkdir -p data/logs data/db data/files data/tmp

echo "🗃️  Recreating database ..."
 mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS backdrop; CREATE DATABASE backdrop'

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
    echo "🐉 Copying over a fresh copy of Backdrop ..."
    rm -rf backdrop
    mkdir backdrop
    cp -rf /backdrop/* /workspace/backdrop
fi

# echo "📁 Adding custom code directories ..."
# dirs=( "modules" "themes" "sites" "layouts" )
# for dir in "${dirs[@]}"
# do
#     if [ ! -f "/workspace/src/$dir" ]
#     then
#         cp -rf "/workspace/backdrop/$dir" "/workspace/src/"
#     fi
#     rm -rf "/workspace/backdrop/$dir"
#     ln -fs "/workspace/src/$dir" "/workspace/backdrop/$dir"
#     chmod -R a+w "/workspace/src/$dir"
# done

chown -R www-data:www-data /workspace/backdrop

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/settings.local.php /workspace/backdrop/settings.local.php

echo "🗳️  Installing Backdrop ..."
bee install --auto --site-name="DropDev Starter Site" \
            --db-name=backdrop --db-user=root --db-pass=root --db-host=db \
            --username=admin --password=admin --email=admin@example.com

echo "🎉\n"

bee uli