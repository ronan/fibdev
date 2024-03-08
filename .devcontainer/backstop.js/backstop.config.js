module.exports = {
  "viewports": [
    {
      "label": "desktop",
      "width": 1920,
      "height": 1080
    }
  ],
  "scenarios": [],
  "paths": {
    "bitmaps_reference": "bitmaps_reference",
    "bitmaps_test": "bitmaps_test",
    "engine_scripts": "engine_scripts",
    "html_report": "html_report",
    "ci_report": "ci_report"
  },
  "report": ["browser"],
  "engine": "playwright",
  "onReadyScript": "onReady.js",
  "engineOptions": {
    "browser": "chromium"
  },
  "misMatchThreshold": 0.5,
  "asyncCaptureLimit": 5,
  "asyncCompareLimit": 10,
  "resembleOutputOptions": {
    "ignoreAntialiasing": true,
    "usePreciseMatching": true
  },
  "debug": false,
  "debugWindow": false
}

require('fs').readFileSync('/workspace/inbox/urls.txt', 'utf-8').split(/\n/).forEach((url) => {
  module.exports.scenarios.push(
    {
      "label": url,
      "url": "http://drupal/" + url,
      "referenceUrl": process.env.PROD_URL + "/" + url,
      "removeSelectors": [".visually-hidden"]
    }
  );
});
