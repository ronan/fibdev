<?php

todo("Generate test scenarios", function () {
  todo("Crawl live site `2` levels deep for urls to test", function ($level = 2) {
    $url = $_ENV['PROD_URL'];
    $domain = parse_url($_ENV['PROD_URL'], PHP_URL_HOST);
    chdir("/workspace/data/tmp");
    shh(
      <<< EOM
      wget --spider \
          --recursive \
          --level=$level \
          --max-redirect=1 \
          --delete-after \
          --domains=$domain \
          $url 2>&1 \
          | grep -B 3 "text/html" \
          | grep http \
          | awk '{ print $3 }' \
          | uniq \
          | sed "s|$url||g" \
          > /workspace/site/outbox/urls.txt
  EOM
    );
  });

  todo("Pick `10` paths to test", function ($num = 10) {
    site_file(
      'inbox/urls.txt',
      array_slice(site_file('outbox/urls.txt'), 0, $num),
      true
    );
  });

  todo("Create Backstop.js config file", function () {
    $scenarios = array();
    foreach (site_file('inbox/urls.txt') as $url) {
      $scenarios[] = <<<EOM
        {
            "label": "$url",
            "url": "http://drupal$url",
            "referenceUrl": "$_ENV[PROD_URL]$url",
            "delay": "500",
            "removeSelectors": [".visually-hidden"]
        }
        EOM;
    }

    $scenarios = implode(',', $scenarios);
    $baskstop_json = <<<EOM
    {
      "viewports": [
        {
          "label": "phone",
          "width": 390,
          "height": 844
        },
        {
          "label": "tablet",
          "width": 1024,
          "height": 768
        },
        {
          "label": "desktop",
          "width": 1920,
          "height": 1080
        }
      ],
      "scenarios": [$scenarios],
      "paths": {
        "engine_scripts":    "engine_scripts",
        "bitmaps_reference": "report/bitmaps_reference",
        "bitmaps_test":      "report/bitmaps_test",
        "html_report":       "report/html"
      },
      "report": ["browser"],
      "engine": "puppeteer",
      "engineOptions": {
        "args": ["--no-sandbox"]
      },
      "misMatchThreshold": 0.5,
      "asyncCaptureLimit": 1,
      "asyncCompareLimit": 10,
      "resembleOutputOptions": {
        "ignoreAntialiasing": true,
        "usePreciseMatching": true
      },
      "debug": false,
      "debugWindow": false,
      "ci": {
        "testSuiteName": "Upgrade"
      }
    }
    EOM;

    site_file('outbox/backstop.json', $baskstop_json, $can_create = true);
  });

  todo("Run backstop.js reference", function () {
    shh('docker exec backstop backstop reference');
  });
});
