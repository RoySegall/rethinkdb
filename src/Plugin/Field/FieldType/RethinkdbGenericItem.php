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
 *   constraints = {}
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

    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    $select = ['---' => $this->t('Select entity type')];
    foreach ($entity_types as $entity_type) {
      if ($entity_type->get('reference_handler') == 'rethinkdb') {
        $select[$entity_type->id()] = $entity_type->getLabel();
      }
    }

    $element['target_type'] = [
      '#type' => 'select',
      '#title' => t('Select RethinkDB based entity'),
      '#options' => $select,
      '#default_value' =>  $this->getSetting('target_type')
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function _fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $field = $form_state->getFormObject()->getEntity();

    // Get all selection plugins for this entity type.
    $selection_plugins = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionGroups($this->getSetting('target_type'));
    $handlers_options = array();
    foreach (array_keys($selection_plugins) as $selection_group_id) {
      // We only display base plugins (e.g. 'default', 'views', ...) and not
      // entity type specific plugins (e.g. 'default:node', 'default:user',
      // ...).
      if (array_key_exists($selection_group_id, $selection_plugins[$selection_group_id])) {
        $handlers_options[$selection_group_id] = Html::escape($selection_plugins[$selection_group_id][$selection_group_id]['label']);
      }
      elseif (array_key_exists($selection_group_id . ':' . $this->getSetting('target_type'), $selection_plugins[$selection_group_id])) {
        $selection_group_plugin = $selection_group_id . ':' . $this->getSetting('target_type');
        $handlers_options[$selection_group_plugin] = Html::escape($selection_plugins[$selection_group_id][$selection_group_plugin]['base_plugin_label']);
      }
    }

    $form = array(
      '#type' => 'container',
      '#process' => array(array(get_class($this), 'fieldSettingsAjaxProcess')),
      '#element_validate' => array(array(get_class($this), 'fieldSettingsFormValidate')),

    );
    $form['handler'] = array(
      '#type' => 'details',
      '#title' => t('Reference type'),
      '#open' => TRUE,
      '#tree' => TRUE,
      '#process' => array(array(get_class($this), 'formProcessMergeParent')),
    );

    $form['handler']['handler'] = array(
      '#type' => 'select',
      '#title' => t('Reference method'),
      '#options' => $handlers_options,
      '#default_value' => $field->getSetting('handler'),
      '#required' => TRUE,
      '#ajax' => TRUE,
      '#limit_validation_errors' => array(),
    );
    $form['handler']['handler_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Change handler'),
      '#limit_validation_errors' => array(),
      '#attributes' => array(
        'class' => array('js-hide'),
      ),
      '#submit' => array(array(get_class($this), 'settingsAjaxSubmit')),
    );

    $form['handler']['handler_settings'] = array(
      '#type' => 'container',
      '#attributes' => array('class' => array('entity_reference-settings')),
    );

    $handler = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field);
    $form['handler']['handler_settings'] += $handler->buildConfigurationForm(array(), $form_state);

    return $form;
  }

}
