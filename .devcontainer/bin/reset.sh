#!/bin/bash
set -e

cd /workspace

echo "Initializing D9->D10 Migration Tool."
echo "🗑️  Deleting logs, databases and temporary files ..."
mkdir -p data
rm -rf   data/logs data/db data/files data/tmp outbox
mkdir -p data/logs data/db data/files data/tmp outbox

echo "🗃️  Recreating databases ..."
mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal9; CREATE DATABASE drupal9'
mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal10; CREATE DATABASE drupal10'

if [ -d /workspace/inbox/code ]
then
    echo "📂 Copying source from inbox ..."
    rm -rf /workspace/drupal9
    cp -rf /workspace/inbox/code /workspace/drupal9
    composer9 require --no-install --with-all-dependencies --ignore-platform-req=php drush/drush
    composer9 install --no-interaction --ignore-platform-req=php

    rm -rf /workspace/drupal10
    cp -rf /workspace/inbox/code /workspace/drupal10
    composer10 require --no-install --with-all-dependencies --ignore-platform-req=php drush/drush
    composer10 install --no-interaction --ignore-platform-req=php

    # extract custom modules/themes to /src
    if [! -d /src/modules ]
    then
        cp /workspace/drupal10/web/modules/custom /src/modules
    fi
    if [! -d /src/themes ]
    then
        cp /workspace/drupal10/web/themes/custom /src/themes
    fi
else
    echo "😵 Please place the site in /workspace/inbox/code"
    exit
fi

if [ -d /workspace/inbox/files ]
then
    rm -rf /workspace/drupal10/web/sites/default/files
    ln -s /workspace/inbox/files /workspace/drupal10/web/sites/default/files
fi

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/drupal9/settings.local.php /workspace/drupal9/web/sites/default/settings.php
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.php

if [ -f /workspace/inbox/*sql ]
then
    echo "🚚 Importing SQL dump ..."
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal9
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10
elif [ -f /workspace/inbox/*sql.gz ]
then
    echo "📦 Importing gzipped SQL dump ..."
    zcat /workspace/inbox/*sql.gz | mariadb -h db -u root -proot drupal9
    zcat /workspace/inbox/*sql.gz | mariadb -h db -u root -proot drupal10
else
    echo "🗳️  Installing Drupal 9 ..."
    drush9 si --db-url=mysql://root:root@db/drupal9 --site-name="D9 Site" -y
    echo "🗳️  Installing Drupal 10 ..."
    drush10 si --db-url=mysql://root:root@db/drupal10 --site-name="D10 Site" -y
fi

composer10 show --direct -f json | jq -r '.installed[] | "\(.name):\(.version)"' > /workspace/outbox/modules.txt

echo "🎉 🎉 🎉"
echo "👇 Drupal 9 login"
drush9 uli
echo "👇 Drupal 10 site login"
drush10 uli
echo "🎉 🎉 🎉"

