<?php
// Reimport env from .env file
// system('set -a; . /workspace/.devcontainer/.env; set +a');
// function error_handler(
//     int $errno,
//     string $errstr,
//     string $errfile = "",
//     int $errline = 0
// ) {
//     err("$errno: $errstr\n$errfile:$errline");
//     return true;
// }
// set_error_handler("error_handler");

function say($msg, $icon = "ℹ️") {
    echo("$icon  $msg\n");
}

function err($err) {
    todo_state($GLOBALS['current_todo'], '!');
    todid("🛑", "failed");

    echo("\n\n$err\n\n");
    die;
}

function todo($key, $icon = "📤", $callback = null) {
    $icon = get_icon($key, $icon);
    [, $indent, $status, $todo] = todo_state($key);
    if ($key != $todo) return false;

    echo("$icon\t" . str_pad("$indent$key ", 59, "."));
    logstr("TODO: $icon\t$key\n");

    $GLOBALS['current_todo'] = $key;
    $GLOBALS['start_time'] = microtime(true);

    switch ($status) {
        case 'x':
            return todid("🆗");
        case '-':
            return todid("⏩️");
        default:
            todo_state($GLOBALS['current_todo'], '.');
            if (file_exists($f = "/workspace/.devcontainer/todos/$key.inc.php")) {
                todid("🔽", "...");
                require($f);
                return;
            }
            if ($callback) {
                $callback();
            }
            todo_check($GLOBALS['current_todo']);
            return todid("✅", round(microtime(true) - $GLOBALS['start_time'], 3) . "s");
    }
}

function todid($icon, $msg="") {
    logstr("TODID: $GLOBALS[current_todo] $icon\t$msg\n");

    echo("$icon\t$msg\n");
    return false;
}

function todo_uncheck($key) {
    todo_state($key, ' ');
}

function todo_check($key) {
    todo_state($key, 'x');
}

function todo_state($key, $val = null) {
    $file = "/workspace/site/README.md";
    if (!file_exists($file)) return $key == 'Create TODO list' ? " " : null;

    $lines = file($file);
    foreach($lines as $i => $line) {
        if ($matches = get_matches("/( *)\- +\[(.)?\] (.+)/", $line)) {
            [, $indent, $status, $todo] = $matches;
            if ($key && $todo == $key) {
                if (!is_null($val)) {
                    $lines[$i] = "$indent- [$val] $todo\n";
                    file_put_contents($file, implode("", $lines));
                }
                return $matches;
            }
        }
    }
    return null;
}

function site_file($path, $lines = null) {
    $path = "/workspace/site/$path";
    if ($lines) {
        file_put_contents($path, implode("\n", $lines));
        return $lines;
    }
    return explode("\n", file_get_contents($path));
}

function config($key, $val = null) {
    return site_config("/^|". preg_quote("**$key:**") . "|(.+)|$/");
}

function site_config($pattern, $replace = null) {
    $config = file_get_contents("/workspace/site/README.md");
    return get_matches($pattern, $config)[0];

    if ($replace) {
        file_put_contents("/workspace/site/README.md", implode("", $replace));
    }
    return file("/workspace/site/README.md");
}

function site_name($site = "") {
    if ($site) return $site;
    if (is_link("/workspace/site")) {
        $target = readlink("/workspace/site");
        return basename($target);
    }
    if (!empty($GLOBALS['ACTIVE_SITE'])) {
        return $GLOBALS['ACTIVE_SITE'];
    }
    return false;
}

function active_site($site = "") {
    if ($site) {
        $GLOBALS['ACTIVE_SITE'] = $site;
    }
    else if (is_link("/workspace/site")) {
        $target = readlink("/workspace/site");
        $GLOBALS['ACTIVE_SITE'] = basename($target);
    }
    if ($GLOBALS['ACTIVE_SITE']) {
        $GLOBALS['site_directory'] = "/workspace/sites/$GLOBALS[ACTIVE_SITE]";
        // TODO: Fix this. It puts symlinks inside the existing ones
        //link_directory($GLOBALS['site_directory'] . "/data", "/workspace/data");
        //link_directory($GLOBALS['site_directory'], "/workspace/site");
        return $GLOBALS['ACTIVE_SITE'];
    }
}

function create_directory($dir, $remove_existing = True) {
    remove_directory($remove_existing);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
 }

function link_directory($from, $to) {
    sh("ln -s -f '$from' '$to'");
}

function remove_directory($dir, $remove_existing = True) {
    if (is_dir($dir) && $remove_existing) {
        system("rm -rf $dir");
    }
}

function get_installed_packages($file, $with_versions=true) {
    $line_template = $with_versions ? "\(.name):\(.version)" : "\(.name)";
    $installed = `composer show --working-dir=/workspace/site/root/ --no-dev --direct -f json | jq -r '.installed[] | "$line_template"'`;
    file_put_contents($file, $installed);
}

function composer($cmd) {
    return shh("composer -n --working-dir=/workspace/site/root/ $cmd");
}

function sh($command = "echo 'sh() was called without a command'") {
    logstr("-----\n# $command\n");
    system($command);
}

function shh($command = "echo 'shh() was called without a command'") {
    logstr("-----\n> $command\n");
    return logstr(`$command 2>&1`);
}

function logstr($msg) {
    $time = date("c");
    file_put_contents('/workspace/data/logs/tasks.log', "$time: $msg\n", FILE_APPEND);
    return $msg;
}

function get_matches($pattern, $subject) {
    $matches = array();
    preg_match($pattern, $subject, $matches);
    return $matches;
}

function get_match($pattern, $subject, $index = 0) {
    $matches = get_matches($pattern, $subject);
    return @$matches[$index];
}

function get_icon($msg, $icon) {
    if ($icon) return $icon;

    $d = [        
        "directory" => "📁",
        "initialize" => "🥚",
        "github" => "🐙",
        "files" => "🗂️",
        "settings" => "📝",
        "recreate" => "🔄",
        "database" => "🗃️",
        "clone" => "🐑",
        "problem" => "🧯",
        "install" => "💾",
        "outdated" => "📜",
        "code" => "📑",
        "patch" => "🪡",
        "unpin" => "📌",
        "pin" => "📍",
        "remove" => "🗑️",
        "module" => "🧩",
        "settings" => "📝",
        "delete" => "🗑️",
        "10" => "🔟",
        "9" => "9️⃣",
        "drupal" => "💧",
        "upgrade" => "🆙",
        "data" => "💽",
        "todo" => "✅",
    ];
    foreach ($d as $k => $v) {
        if (stripos($msg, $k) !== false) return $v;
    }
    return '🎲';
}