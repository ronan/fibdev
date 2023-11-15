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

define("REGEX_KEYVAL", "/( *)\- +\[(.)?\] (.+)/");

function say($msg, $icon = "ℹ️") {
    echo("$icon  $msg\n");
}

function err($err) {
    todo_state($GLOBALS['current_todo'], '!');
    todid("🛑", "failed");

    echo("\n\n$err\n\n");
    die;
}

function todo_regex($key = null) {
    $key = $key ? preg_quote($key) : '.+';
    return "/( *)\- +\[(.)?\] ($key)/";
}

function todo($key, $callback = null) {
    $todo = todo_load($key);
    if (!$todo) return;

    echo(get_icon($todo['key']) . "\t" . str_pad("$todo[indent]$todo[key] ", 59, "."));
    switch ($todo['status']) {
        case 'x':
            return todid("🆗");
        case '-':
            return todid("⏩️");
        default:
            todo_state($todo['key'], '.');
            $insert_after = $todo['regex'];
            foreach ($todo['sub_todos'] as $sub) {
                if (!todo_load($sub['key'])) {
                    site_file_insert_after(
                        $insert_after, 
                        "$todo[indent]  $sub[indent]- [ ] $sub[key]"
                    );
                    $insert_after = $sub['regex'];
                }
            }
            if (file_exists($f = "/workspace/.devcontainer/todos/$todo[key].inc.php")) {
                todid("🔽", "...");
                require($f);
            }
            $start_time = microtime(true);
            if ($callback) {
                $callback();
            }
            todo_state($todo['key'], 'x');
            todid("✅", round(microtime(true) - $start_time, 3) . "s");
        }
}

function todid($icon, $msg="") {
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
    if (!file_exists($file)) return null;

    $todo = todo_load($key);
    if($todo && $val) {
        site_file_replace($todo['regex'], "$todo[indent]- [$val] $todo[key]");
    }
    return isset($todo['status']) ? $todo['status'] : null;
}

function todo_load($key = null, $file = 'README.md') {
    $lines = site_file($file);
    $out = [];
    foreach ($lines as $i => $line) {
        if ($matches = get_matches(todo_regex($key), $line)) {
            $todo = array_combine(
                ['todo', 'indent', 'status', 'key'],
                $matches
            );          
            $todo['line'] = $i;
            $todo['regex'] = todo_regex($todo['key']);
            $todo['sub_todos'] = todo_load_all("/workspace/.devcontainer/todos/$todo[key].md");
            if ($key) return $todo;
            $out[$i] = $todo;
        }
    }
    return $out;
}

function todo_load_all($file = 'README.md') {
    return todo_load(null, $file);
}

function site_file($path = "README.md", $replace = null) {
    if (!$path = site_file_path($path)) {
        return [];
    }
    if ($replace) {
        file_put_contents($path, implode("\n", $replace));
    }
    return explode("\n", file_get_contents($path));
}

function site_file_path($path) {
    if (substr($path, 0, 1) == '/' && file_exists($path)) return $path;

    foreach (
        [
            '/workspace/site',
            '/workspace/.devcontainer/site'
        ] as $base
    ) {
        if (file_exists("$base/$path")) {
            return "$base/$path";
        }
    }
    return false;
}

function site_file_replace($pattern, $new_lines, $file = "README.md") {
    site_file_insert_after($pattern, $new_lines, 1, $file);
}

function site_file_insert_after($pattern, $new_lines, $length = 0, $file = "README.md") {
    $new_lines = is_array($new_lines) ? $new_lines : array($new_lines);
    $lines = site_file($file);
    foreach (get_matches_array($pattern, $lines) as $i => $m) {
        array_splice($lines, $i + 1 - $length, $length, $new_lines);
        site_file($file, $lines);
        return;
    }
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

function get_matches($pattern, $line) {
    $matches = array();
    preg_match($pattern, $line, $matches);
    return $matches;
}

function get_matches_array($pattern, $lines) {
    $out = array();
    foreach ($lines as $i => $line) {
        if ($matches = get_matches($pattern, $line)) {
            $out[$i] = $matches;
        }
    }
    return $out;
}


function get_icon($msg) {
    if (!$msg) return "";
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