<?php

todo("Prepare the codebase", function() {
  todo("Configure the git repoository url", function() {
    $repo = todo_config('Incompatible Modules', 'Git Repo');
    $out = $repo ? 'x' : ' ';
    return <<< EOM
$out
|   |   |
| - | - |
| **Git Repo** | $repo | 
EOM;
  });

});