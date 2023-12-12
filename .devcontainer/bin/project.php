#!/usr/local/bin/php
<?php
require_once "helpers.inc.php";

$input = isset($argv[1]) ? $argv[1] : "";

if ($input && file_exists("/workspace/sites/$input")) {
  active_site($input);
}
