<?php

namespace Drupal\savethebw\Plugin\DsField\menu_link_content;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * @DsField(
 *   id = "menu_item_title",
 *   title = @Translation("Menu item title"),
 *   entity_type = "menu_link_content",
 *   provider = "menu_item_extras"
 * )
 */
class MenuItemTitle extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = $this->entity();
    $title = $entity->title->value;
    return [
      '#template' => '{{ title }}',
      '#type' => 'inline_template',
      '#context' => [
        'title' => $title,
      ],
    ];
  }
}
