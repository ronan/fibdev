#!/bin/bash

if [ ! -f /workspace/inbox/urls.txt ]
then 
    echo "🕷️ Crawling site to gather urls to test"
    mkdir -p /data/wget
    cd /data/wget
    wget --spider \
         --recursive \
         --level=5 \
         --max-redirect=0 \
         --delete-after \
         --domains=drupal9 \
        http://drupal9/ 2>&1 \
        | grep -B 3 "text/html" \
        | grep http \
        | awk '{ print $3 }' \
        | uniq \
        | sed "s|http://drupal9||g" \
        > /workspace/outbox/urls.txt

    cp /workspace/outbox/urls.txt /workspace/inbox/urls.txt
fi

echo "🐒 Creating a backstop.json file"
. /usr/bin/mo
export TEST_URLS=(`head -n 50 /workspace/inbox/urls.txt | tr "\n" " "`)
cat /workspace/.devcontainer/backstop.js/backstop.json.mustache \
    | mo \
    | jq \
    > /workspace/data/backstop/backstop.json

echo "🧹Clearing cache"
# drush9 cache:rebuild
# drush10 cache:rebuild

echo "👓 Peforming visual regression test ..."
docker exec -it backstop backstop reference
docker exec -it backstop backstop test
echo "📊 http://localhost:3000/html_report/index.html?remote"