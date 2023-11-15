#!/usr/local/bin/php
<?php
require_once "helpers.inc.php";

$GIT_REPO_REGEX = "/((git|ssh|http(s)?)|(git@[\w\.]+))(:(//)?)([\w\.@\:/\-~]+)(\.git)(/)?/";

$input = isset($argv[1]) ? $argv[1] : "";

if ($input && file_exists("/workspace/sites/$input")) {
  active_site($input);
}
else if ($input && $m = get_matches($GIT_REPO_REGEX, $input)) {
  // Create a new directory from the given repo. Ask for a name or get it from the path.
  // active_site($m[6]);
}

if (!active_site()) {
  $examples = "\n\nUsage:\n  run git@example.com/foo/bar.git\n";
  foreach (glob("/workspace/sites/*") as $site) {
    $name = basename($site);
    $examples .= "  run $name\n";
  }
  say("There is no currently active site. Please specify a site or git repo. $examples");
  die;
}

say("Current Site: " . config("Site Name"), "📣");
say("Site Directory: $GLOBALS[site_directory]", "📁");


todo("Set up site");
todo("Upgrade to Drupal 10");