#!/usr/bin/php
<?php

include_once "helpers.inc.php";

function todo($key, $callback = null)
{
    static $depth = 0, $last = null, $start_time = 0;

    if (!$lines = todo_file()) {
        $lines = todo_file([
            '# Project READ ME', 
            '', 
            '',
            '## Todo', 
            '',
            '- [x] Configure Project',
            '',
            '    > Uncheck the box above to start.'
        ]);
    }

    $todo = todo_read($key, $lines);
    if (!$todo && $last) {
        if ($todo['end']) {
            array_splice(
                $lines,
                $todo['end'] + 1, 0, 
                [str_repeat('  ', $depth) . "- [ ] $key"]
            );
            $lines = todo_file($lines);
        }
        $todo = todo_read($key, $lines);
    }
    if (!$todo) die("Could not load the todo: $key");

    $last = $todo;
    if (!in_array($todo['status'], ['x', '-'])) {
        $depth++;
        todo_write($todo, '.', $lines);
        site_file_include("todos/$todo[key].inc.php");
        $output = $callback ? call_user_func_array($callback, $todo['args']) : '';
        if (empty($output)) {
            $output = ($last['key'] == $todo['key']) ? 'x' : '~';
        }
        todo_write($todo, $output, $lines);
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


function todo_write($todo, $result = null, $lines)
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

    if ($idx = get_match_index($todo['regex'], $lines)) {
        array_splice($lines, $idx, $todo['length'], $new_lines);
        todo_file($lines);
    }

    return todo_read($todo['key']);
}

function todo_read($key = null, $lines)
{
    $key = preg_replace("/([`\*]+).+\1/", "`(.*)`", preg_quote($key));
    $regex = "|( *)\- +\[(.)?\] ($key)|";

    foreach (get_matches_array($regex, $lines) as $i => $m) {
        array_shift($m);
        $todo['indent'] = array_shift($m);
        $todo['status'] = array_shift($m);
        $todo['key']    = array_shift($m);
        array_shift($m);
        $todo['regex']  = $regex;
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

function todo_file($lines = null) {
    if (!$path = site_file_path($path)) {
        return [];
    }
    if ($replace) {
        file_put_contents($path, implode("\n", $replace));
    }
    return explode("\n", file_get_contents($path));
}
