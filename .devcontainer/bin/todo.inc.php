<?php

function todo($key, $callback = null)
{
    static $depth = 0, $last = null, $start_time = 0;

    $todo = todo_read($key);
    if (!$todo && $last) {
        $todo = todo_insert_after($last, $key, $depth);
    }
    if (!$todo) return;

    if ($last && !empty($last['start'])) {
        echo ("✅\t" . round(microtime(true) - $last['start'], 3) . "s\n");
    }
    echo (get_icon($todo['key']) . "\t" . str_pad("$todo[indent]$todo[key] ", 59, "."));

    $last = $todo;
    switch ($todo['status']) {
        case 'x':
            echo ("🆗\n");
            break;
        case '-':
            echo ("⏩️\n");
            break;
        case '~':
            echo ("⤵️\n");
        default:
            $depth++;

            $todo = todo_state($todo, '.');
            site_file_include("todos/$todo[key].inc.php");
            $output = $callback ? call_user_func_array($callback, $todo['args']) : '';
            if (empty($output)) {
                $output = ($last['key'] == $todo['key']) ? 'x' : '~';
            }
            todo_write($todo, $output);

            $depth--;
    }
}

function todo_regex($key = null)
{
    $key = $key ? preg_quote($key) : '\V+';
    $key = preg_replace("/`.+`/", "`(.*)`", $key);
    return "|( *)\- +\[(.)?\] ($key)|";
}

function todid($icon, $msg = "")
{
    echo ("$icon\t$msg\n");
    return false;
}

function todo_uncheck($todo)
{
    todo_state($todo, ' ');
}

function todo_check($todo)
{
    todo_state($todo, 'x');
}

function todo_state($todo, $val = null)
{
    if (is_string($todo)) $todo = todo_read($todo);
    return todo_write($todo, $val);
}

function todo_write($todo, $result = null)
{
    if (is_string($result)) {
        $todo['status'] = substr($result, 0, 1);
        if (strlen($result) > 1) {
            $todo['output'] = explode("\n", trim(substr($result, 1), "\n"));
        }
    }
    
    $new_lines = ["$todo[indent]- [$todo[status]] $todo[key]"];
    if ($todo['output']) {
        $new_lines = array_merge(
            $new_lines,
            [''],
            todo_indent($todo['output'], strlen($todo['indent']) + 2),
            [''],
        );
    }

    $lines = site_file();
    if ($idx = get_match_index($todo['regex'], $lines)) {
        array_splice($lines, $idx, $todo['length'], $new_lines);
        site_file("README.md", $lines);
    }

    return todo_read($todo['key']);
}

function todo_read($key = null, $file = 'README.md')
{
    $lines = site_file($file);
    foreach (get_matches_array(todo_regex($key), $lines) as $i => $m) {
        array_shift($m);
        $todo['indent'] = array_shift($m);
        $todo['status'] = array_shift($m);
        $todo['key']    = array_shift($m);
        array_shift($m);
        $todo['regex']  = todo_regex($key);
        $todo['args']   = $m;
        $todo['line']   = $i;
        $todo['file']   = $file;
        $todo['length'] = 1;
        $todo['start']  = microtime(true);
        $todo['output'] = [];

        $indent = $todo['indent'] . "  ";
        while (
             ++$i < count($lines) &&
             !str_starts_with($lines[$i], "$indent- [") &&
             (
                empty($lines[$i]) || str_starts_with($lines[$i], $indent)
             )
        ) {
            $todo['output'][] = $lines[$i];
        }
        $todo['output'] = todo_trim_newlines($todo['output']);
        $todo['output'] = todo_outdent($todo['output']);
        if ($todo['output']) {
            $todo['length'] += count($todo['output']) + 2;
        }
        $todo['end'] = $todo['line'] + $todo['length'];

        while (++$i < count($lines) && str_starts_with($lines[$i], $indent)) {
            $todo['end']++;
        }
        return $todo;
    }
}

function todo_insert_after($todo, $key, $depth)
{
    $line = str_repeat('  ', $depth) . "- [ ] $key";
    if ($idx = $todo['end']) {
        $lines = site_file("README.md");
        array_splice($lines, $idx + 1, 0, $line);
        site_file("README.md", $lines);
    }
    return todo_read($key);
}

function todo_trim_newlines($lines) {
    if (empty($lines)) return $lines;
    return explode("\n", trim(implode("\n", $lines), "\n"));
}

function todo_outdent($lines) {
    $depth = strlen($lines[0]) - strlen(ltrim($lines[0]));
    return array_map(function($line) use ($depth) {
        return substr($line, $depth);
    }, $lines);
}

function todo_indent($lines, $depth) {
    return array_map(function($line) use ($depth) {
        return str_repeat(' ', $depth) . $line;
    }, $lines);
}