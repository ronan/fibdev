module.exports = {
  "viewports": [
    {
      "label": "desktop",
      "width": 1920,
      "height": 1080
    },
    {
      "label": "mobile",
      "width": 360,
      "height": 800
    }
  ],
  "scenarios": [],
  "paths": {
    "engine_scripts":     "/workspace/inbox/backstop.js/engine_scripts",
    "html_report":        "/workspace/outbox/backstop.js/report",
    "bitmaps_reference":  "/workspace/outbox/backstop.js/report/bitmaps_reference",
    "bitmaps_test":       "/workspace/outbox/backstop.js/report/bitmaps_test",
    "ci_report":          "/workspace/outbox/backstop.js/report/ci"
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

try {
  require('fs').readFileSync('/workspace/inbox/urls.txt', 'utf-8')
                .split(/\n/)
                .splice(0, process.env.PAGES_TO_TEST || 100)
                .forEach((url) => {
                            module.exports.scenarios.push(
                              {
                                "label": url,
                                "url": `http://static:8009${url}`,
                                "referenceUrl": `http://drupal${url}`,
                                "removeSelectors": [".visually-hidden"]
                              }
                            );
                          });
} catch (err) {
}
