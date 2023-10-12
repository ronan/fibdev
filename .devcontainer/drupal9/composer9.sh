#!/bin/sh

COMPOSER_DISCARD_CHANGES=true yes | composer --working-dir=/workspace/drupal9/ -n $@