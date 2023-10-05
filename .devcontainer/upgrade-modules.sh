#!/bin/bash

echo "📌 Unpinning module versions ..."
# composer10 show --direct -f json | jq -r '.installed[] | "\(.name)"' > /workspace/outbox/updatable-modules.txt
cat /workspace/outbox/updatable-modules.txt | tr "\n" " " | xargs composer10 require --no-update --no-audit --ignore-platform-req=php

echo "🗑️ Composer remove rogue modules ..." 
cat /workspace/outbox/unupdatable-modules.txt | tr "\n" " " | xargs composer10 remove --no-update --no-audit

echo "📦 Composer update ..." 
composer10 update --no-install --with-all-dependencies --ignore-platform-req=php
