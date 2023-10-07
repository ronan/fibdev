<?php

namespace Drupal\savethebw\Plugin\Block;

use Drupal\block\Entity\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a site alerts block
 *
 * @Block(
 *   id = "site_alerts",
 *   admin_label = @Translation("Site alerts"),
 * )
 */
class Alerts extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['savethebw:alerts']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $nid = \Drupal::routeMatch()->getRawParameter('node');
    if ($nid) {
      $current_node = $node_storage->load($nid);
      if ($current_node->bundle() == 'alert') {
        return [ $view_builder->view($current_node, 'alert') ];
      }
    }
    $alerts = array_values(\Drupal::entityQuery('node')
      ->condition('type', 'alert')
      ->condition('status', 1)
      ->sort('created' , 'DESC')
      ->execute());
    $content = [];
    foreach ($alerts as $nid) {
      $node = $node_storage->load($nid);
      if ($node->hasField('field_page_visibility') && !$node->field_page_visibility->isEmpty()) {
        $conditionManager = \Drupal::service('plugin.manager.condition');
        $condition = $conditionManager->createInstance('request_path');
        $condition->setConfiguration([
          'id' => 'request_path',
          'pages' => $node->field_page_visibility->value,
          'negate' => (bool) (int) $node->field_include_or_exclude->value,
        ]);
        if (($condition->evaluate() && !$condition->isNegated()) ||
            (!$condition->evaluate() && $condition->isNegated())) {
          $content[] = $view_builder->view($node, 'alert');
          break;
        }
      }
    }
    return $content;
  }
}
