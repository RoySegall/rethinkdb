<?php

namespace Drupal\rethinkdb\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldType(
 *   id = "rethinkdb_generic",
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

//  public static function defaultStorageSettings() {
//    return array(
//      'target_type' => \Drupal::moduleHandler()->moduleExists('node') ? 'node' : 'user',
//    ) + parent::defaultStorageSettings();
//  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
      'handler' => 'default:rethinkdb',
    ) + parent::defaultFieldSettings();
  }

  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);

    $entity_types = \Drupal::entityTypeManager()->getDefinitions();

    $select = ['---' => $this->t('Select entity type')];
    foreach ($entity_types as $entity_type) {
      if ($entity_type->get('rethink')) {
        $select[$entity_type->id()] = $entity_type->getLabel();
      }
    }

    $form['handler']['handler_settings']['entity_type'] = array(
      '#type' => 'select',
      '#title' => t('Select RethinkDB based entity'),
      '#options' => $select,
    );

    dpm($form['handler']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];

    return $element;
  }

}
