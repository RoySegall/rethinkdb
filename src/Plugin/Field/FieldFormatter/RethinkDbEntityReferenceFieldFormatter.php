<?php

namespace Drupal\rethinkdb\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin for displaying RethinkDB referenced document property.
 *
 * @FieldFormatter(
 *   id = "rethinkdb_entity_reference",
 *   label = @Translation("RethinkDB document property"),
 *   description = @Translation("Display a specific property of the document."),
 *   field_types = {
 *     "rethinkdb"
 *   }
 * )
 */
class RethinkDbEntityReferenceFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'key' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['key'] = array(
      '#type' => 'textfield',
      '#title' => t('Property name'),
      '#description' => $this->t('Which key of the document would you like to display.'),
      '#default_value' => $this->getSetting('key'),
      '#required' => TRUE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    if ($key = $this->getSetting('key')) {
      $params = [
        '%key' => $key,
      ];
      $text = $this->t('Display %key from the JSON document', $params);
    }
    else {
      $text = t('No property to display');
    }

    $summary[] = $text;

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $item) {
      $entity = $item->getValue();
      $values = $entity->getValues();
      $elements[] = [
        '#plain_text' => $values[$this->getSetting('key')],
      ];
    }

    return $elements;
  }

}
