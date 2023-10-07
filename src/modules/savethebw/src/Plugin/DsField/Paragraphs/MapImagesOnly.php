<?php

namespace Drupal\savethebw\Plugin\DsField\Paragraphs;

use Drupal\ds\Plugin\DsField\DsFieldBase;
use \Drupal\node\Entity\Node;

/**
 * @DsField(
 *   id = "savethebw_map_images",
 *   title = @Translation("Map slide images"),
 *   entity_type = "paragraph",
 *   provider = "paragraphs",
 *   ui_limit = {"map_slides|*"}
 * )
 */
class MapImagesOnly extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $paragraph = $this->entity();
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('paragraph');
    return $view_builder->view($paragraph, 'images_only');
  }

}
