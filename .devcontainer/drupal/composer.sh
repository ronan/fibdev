#!/bin/sh

COMPOSER_DISCARD_CHANGES=true yes | /usr/local/bin/composer --working-dir=/workspace/site/root/ -n $@