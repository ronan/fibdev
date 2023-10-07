<?php

namespace Drupal\savethebw\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Plugin implementation of the "anchor_link" formatter. Provides a link
 * with a class derived from the plain text field passed to it.
 *
 * @FieldFormatter(
 *   id = "anchor_link",
 *   label = @Translation("Anchor link"),
 *   field_types = {
 *     "string",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class AnchorLinkFormatter extends StringFormatter {

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

    foreach ($items as $delta => $item) {
      if ($item->value) {
        $elements[$delta] = [
          '#type' => 'inline_template',
          '#template' => '<a class="nav-link" href="#{{ id }}">{{ title }}</a>',
          '#context' => [
            'id' => Html::cleanCssIdentifier(strtolower($item->value)),
            'title' => $item->value,
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