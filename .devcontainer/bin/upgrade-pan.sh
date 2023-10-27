#!/bin/bash
set -e

cat << EOM
                                   🌤️
    ═╦════╗
     ║  [ | ]
  ___╩___      [ | ][ | ][ | ]
  \   🛟  |     [ 9️⃣ ][ ⇨ ][ 🔟]  _________
   \     |_[| ][ | ][ | ][ | ]_/ o o o /
    \__________________________________/
💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧💧
EOM


cd /workspace/drupal10

echo "🔄 Reset upgrade destination ..."
git restore .

echo "🧼 Prepping git ..."
# https://docs.pantheon.io/guides/drupal-hosted-createcustom/new-branch
git remote add ic https://github.com/pantheon-upstreams/drupal-composer-managed.git && git fetch ic
# git checkout -b composerify
# Todo: Move the custom modules out of the way first
git rm -rf ./*
# git commit -m "Removing all files"
git checkout ic/main .
# git commit -m "Add and commit Integrated Composer files"

# TODO: verify the gitignore is right.
# Note anything not in .gitignore won't get built by pantheon build tools so:
# add web/libraries to gitignore and then pull libraries out into a merged composer file.

# TODO: Automatically create a merge file with the following format:
REPOTPL= <<EOM
"$NAME": {
    "type": "package",
    "package": {
        "name": "$NAME",
        "type": "drupal-library",
        "version": "$VER",
        "dist": {
            "url": "https://github.com/$NAME/archive/refs/tags/v$VER.zip",
            "type": "zip"
        }
    }
}
EOM
REPOTPL= <<EOM
"$NAME": {
    "type": "package",
    "package": {
        "name": "$NAME",
        "type": "drupal-library",
        "dist": {
            "url": "$URL",
            "type": "zip"
        }
    }
}
EOM

# composer10 require $NAME:$VER