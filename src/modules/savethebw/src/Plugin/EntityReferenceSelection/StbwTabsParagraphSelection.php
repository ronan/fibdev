<?php

namespace Drupal\savethebw\Plugin\EntityReferenceSelection;

use Drupal\savethebw\Plugin\EntityReferenceSelection\StbwParagraphSelection;

/**
 * Allows the use of a standard set of components.
 *
 * @EntityReferenceSelection(
 *   id = "savethebw_default_tabs",
 *   label = @Translation("Save the BW defaults (tabs)"),
 *   group = "savethebw_default_tabs",
 *   entity_types = {"paragraph"},
 *   weight = 1
 * )
 */
class StbwTabsParagraphSelection extends StbwParagraphSelection {

  /**
   * {@inheritdoc}
   */
  public function getSortedAllowedTypes() {
    $return_bundles = parent::getSortedAllowedTypes();
    unset($return_bundles['tabs_group']);
    unset($return_bundles['everyaction_embed']);
    return $return_bundles;
  }

}
