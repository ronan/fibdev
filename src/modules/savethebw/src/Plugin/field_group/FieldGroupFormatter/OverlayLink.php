<?php

namespace Drupal\savethebw\Plugin\field_group\FieldGroupFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;
use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'overlay_link' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "overlay_link",
 *   label = @Translation("Overlay Link"),
 *   description = @Translation("Renders inner content inside a modal link to the overlay display."),
 *   supported_contexts = {
 *     "view",
 *   }
 * )
 */
class OverlayLink extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {
    parent::preRender($element, $rendering_object);

    $entity = NULL;
    if (!empty($rendering_object['#node'])) {
      $entity = $rendering_object['#node'];
    }

    if (!empty($rendering_object['#paragraph'])) {
      $entity = $rendering_object['#paragraph'];
    }

    if (!$entity || ($entity->hasField('field_bio') && $entity->field_bio->isEmpty())) {
      $element['#type'] = 'field_group_html_element';
      $element['#wrapper_element'] = 'div';
    }
    else {
      // Set attributes for modal trigger
      $trigger_attributes = new Attribute();
      $trigger_attributes['data-target'] = '#overlay-' . $entity->uuid();
      $trigger_attributes['data-toggle'] = 'modal';
      $trigger_attributes['type'] = 'button';
      // Set attributes for modal window
      $modal_attributes = new Attribute();
      $modal_attributes['aria-hidden'] = 'hidden';
      $modal_attributes['aria-labelledby'] = 'overlay-title-' . $entity->uuid();
      $modal_attributes['class'] = ['modal', 'fade', $entity->bundle()];
      $modal_attributes['data-slug'] = Html::cleanCssIdentifier($entity->title->value);
      $modal_attributes['id'] = 'overlay-' . $entity->uuid();
      $modal_attributes['tabindex'] = "-1";
      // Set header attributes
      $header_attributes = new Attribute();
      $header_attributes['class'] = ['modal-title', 'sr-only'];
      $header_attributes['id'] = 'overlay-title-' . $entity->uuid();
      // Remove children from wrapper element
      $children = [];
      foreach ($element as $key => $value) {
        if (strpos($key, '#') === FALSE) {
          unset($element[$key]);
          $children[$key] = $value;
        }
      }
      $element['wrapped'] = [
        '#type' => 'inline_template',
        '#template' => <<<EOT
          <button {{ trigger_attributes }}>{{ children }}</button>
          <div {{ modal_attributes }}>
            <div class="modal-dialog" role="document">
              <div class="modal-header">
                <h2 {{ header_attributes }}>{{ title }}</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-content">
                {{ drupal_entity(type, id, 'overlay') }}
              </div>
            </div>
          </div>
        EOT,
        '#context' => [
          'children' => $children,
          'header_attributes' => $header_attributes,
          'id' => $entity->id(),
          'modal_attributes' => $modal_attributes,
          'title' => !empty($entity->title->value) ? $entity->title->value : '',
          'trigger_attributes' => $trigger_attributes,
          'type' => $element['#entity_type'],
        ],
      ];
    }
  }

}
