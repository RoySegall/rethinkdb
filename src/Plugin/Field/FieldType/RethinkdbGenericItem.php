<?php

namespace Drupal\rethinkdb\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldType(
 *   id = "rethinkdb",
 *   label = @Translation("Rethink DB Reference"),
 *   description = @Translation("This field stores the ID of a file as an integer value."),
 *   category = @Translation("Reference"),
 *   default_widget = "entity_reference_autocomplete",
 *   default_formatter = "entity_reference_label",
 *   list_class = "\Drupal\rethinkdb\Plugin\Field\FieldType\RethinkDBFieldItemList",
 *   constraints = {"ReferenceAccess" = {}, "FileValidation" = {}}
 * )
 */
class RethinkdbGenericItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'target_id' => array(
          'description' => 'The ID of the file entity.',
          'type' => 'varchar_ascii',
          'length' => 11,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return [];
  }

}
