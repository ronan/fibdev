#!/bin/bash
set -e

cat << EOM

    ═╦════╗                         🌤️
     ║  [ | ]
  ___╩___      [  ][  ][  ]
  \   🛟  |     [  ][  ][  ]  ________
   \     |_[  ][  ][  ][  ]_/ o o o /
    \______________________________/
🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊🌊
EOM

cd /workspace

echo "Initializing D9->D10 Migration Tool."
echo "🗑️  Deleting logs, databases and temporary files ..."
mkdir -p data
rm -rf   data/logs data/db data/files data/tmp outbox
mkdir -p data/logs data/db data/files data/tmp outbox


if [ -d /workspace/inbox/code ]
then
    echo "📂 Copying source from inbox ..."
    rm -rf /workspace/drupal9 /workspace/drupal10
    cp -r  /workspace/inbox/code/ /workspace/drupal9
    cp -r  /workspace/inbox/code/ /workspace/drupal10

    echo "💾 Composer install d9 ..."
    composer9 install --no-interaction --ignore-platform-reqs

    echo "🗂️ Exfiltrate custom modules and themes ..."
    if [ ! -d /workspace/src/modules ]
    then
        ln -s /workspace/drupal10/web/modules/custom /workspace/src/modules
    fi
    if [ ! -d /workspace/src/themes ]
    then
        ln -s /workspace/drupal10/web/themes/custom /workspace/src/themes
    fi
else
    echo "😵 Please place the site in /workspace/inbox/code"
    exit
fi

if [ -d /workspace/inbox/files ]
then
    rm -rf /workspace/drupal10/web/sites/default/files
    ln -s /workspace/inbox/files /workspace/drupal10/web/sites/default
    rm -rf /workspace/drupal9/web/sites/default/files
    ln -s /workspace/inbox/files /workspace/drupal9/web/sites/default
fi

echo "📝 Adding local settings ..."
cp -f /workspace/.devcontainer/drupal9/settings.local.php /workspace/drupal9/web/sites/default/settings.local.php
cp -f /workspace/.devcontainer/drupal10/settings.local.php /workspace/drupal10/web/sites/default/settings.local.php

echo "🗃️  Recreating databases ..."
mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal9; CREATE DATABASE drupal9'
mariadb -h db --password=root -e 'DROP DATABASE IF EXISTS drupal10; CREATE DATABASE drupal10'
if compgen -G /workspace/inbox/*sql > /dev/null;
then
    echo "🚚 Importing SQL dump ..."
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal9
    cat /workspace/inbox/*sql | mariadb -h db -u root -proot drupal10
elif compgen -G /workspace/inbox/*sql > /dev/null;
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

echo "🎉 🎉 🎉"
echo "👇 Drupal 9 login"
drush9 uli
# echo "👇 Drupal 10 site login"
# drush10 uli
echo "🎉 🎉 🎉"

