const fs = require('fs');
const { parse } = require("csv-parse/sync");
const Mustache = require('mustache');
const moment = require('moment');

console.log("🏁 Building HTML 🏁");


const view = {
  project: "Wu Tsai Neurosciences Institute",
  snapshots: () => {
    out = [];
    keys = [
      'date', 'commit', 'db', 'kind', 'version', 'env', 'pages', 'failures',
    ]
    csv = fs.readFileSync('/workspace/outbox/snapshots.csv', 'utf-8');
    out = parse(csv, { delimiter: ",", from_line: 2 }).map((row) => {
      var record = keys.reduce((obj, key, index) => ({ ...obj, [key]: row[index] }), {});
      record.date_abs = moment(record.date).format("M/D/Y");
      record.date_rel = moment(record.date).fromNow();
      record.date_path = moment(record.date).format("YYYY-MM-DDTHH-mm-ss");
      return record;
    })
    return out;
  }
};

tpl = fs.readFileSync('src/index.html.mo', 'utf-8')

const output = Mustache.render(tpl, view);

require('fs').writeFile('/workspace/outbox/dashboard/index.html', output, err => {
  if (err) {
    console.error(err);
  } else {
    console.log("🎉 Done! 🎉")
  }
});