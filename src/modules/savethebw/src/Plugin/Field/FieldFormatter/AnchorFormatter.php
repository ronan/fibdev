<?php

namespace Drupal\savethebw\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the "anchor_element" formatter. Provides an element
 * with an id based on the plain text field passed to it for in-page linking.
 *
 * @FieldFormatter(
 *   id = "anchor_element",
 *   label = @Translation("Anchor element"),
 *   field_types = {
 *     "string",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class AnchorFormatter extends StringFormatter {

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
    $elements = [];
    $entity = $items->getEntity();

    foreach ($items as $delta => $item) {
      if ($item->value) {
        $elements[$delta] = [
          '#type' => 'inline_template',
          '#template' => '<div class="anchor-link" data-deferred-anchor-id="{{ id }}">{{ title }}</div>',
          '#context' => [
            'id' => Html::cleanCssIdentifier(strtolower($item->value)),
            'title' => $entity->bundle() == 'accordion_item' ? $item->value : '',
          ],
          '#attached' => [
            'library' => [ 'savethebw/anchor' ],
          ],
        ];
      }
    }
    return $elements;
  }

}