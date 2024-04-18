<?php

use Drupal\node\Entity\Node;


$node = \Drupal::entityTypeManager()->getStorage('node')->load(111);

$configuration = $node->get('layout_builder__layout')->get(1)->get('section')->getValue()->getComponent('ea6c13e3-10b8-49cd-93eb-cad9c5d3f610')->get('configuration');
$configuration['id'] = 'views_block:events_listing-bl_events_listing';
$node->get('layout_builder__layout')->get(1)->get('section')->getValue()->getComponent('ea6c13e3-10b8-49cd-93eb-cad9c5d3f610')->setConfiguration($configuration);
$node->save();