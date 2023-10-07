<?php

namespace Drupal\savethebw\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * 
 *
 * @FieldFormatter(
 *   id = "featured_boolean",
 *   label = @Translation("Featured?"),
 *   field_types = {
 *     "boolean",
 *   }
 * )
 */
class FeaturedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $entity = $items->getEntity();
    $elements = [];

    foreach ($items as $delta => $item) {
      $is_featured = (bool) (int) $item->getValue()['value'];
      if ($is_featured) {
        $elements[$delta] = [
          '#type' => 'inline_template',
          '#template' => '<span class="is-featured">FEATURED</span>',
        ];
      }
    }

    return $elements;
  }

}
