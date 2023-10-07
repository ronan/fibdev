<?php

namespace Drupal\savethebw\Plugin\EntityReferenceSelection;

use Drupal\paragraphs\Plugin\EntityReferenceSelection\ParagraphSelection;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Allows the use of a standard set of components.
 *
 * @EntityReferenceSelection(
 *   id = "savethebw_default",
 *   label = @Translation("Save the BW defaults"),
 *   group = "savethebw_default",
 *   entity_types = {"paragraph"},
 *   weight = 1
 * )
 */
class StbwParagraphSelection extends ParagraphSelection {
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSortedAllowedTypes() {
    $return_bundles = [];

    // Nested bundles to exclude from the standard selection. 
    $exclude = [
      'accordion_item',
      'freeform_grid_item',
      'homepage_banner_item',
      'image',
      'logo',
      'map_slide',
      'metric',
      'reference_grid_item',
      'resource_embed',
      'resource_file',
      'resource_link',
      'resource_video',
      'tab',
      'teaser_grid_item',
      'text_grid_item',
    ];
    
    $bundles = $this->entityTypeBundleInfo->getBundleInfo('paragraph');
    $weight = 0;

    foreach ($bundles as $machine_name => $bundle) {
      $return_bundles[$machine_name] = [
        'label' => $bundle['label'],
        'weight' => $weight,
      ];

      $weight++;
    }
    uasort($return_bundles, 'Drupal\Component\Utility\SortArray::sortByWeightElement');

    foreach ($exclude as $bundle) unset($return_bundles[$bundle]);

    return $return_bundles;
  }

}
