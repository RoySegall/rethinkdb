<?php

/**
 * @contains \Drupal\rethinkdb\Entity\AbstractRethinkDbEntity.
 */
namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Wrapping the base content entity class. This is used when we need to add
 * un-defined entity keys.
 *
 * When writing the object to RethinkDB
 */
abstract class AbstractRethinkDbEntity extends ContentEntityBase {

  public $values;

  /**
   * {@inheritdoc}
   */
  public function ___construct(array $values, $entity_type, $bundle = FALSE, $translations = array()) {
    parent::__construct($values, $entity_type, $bundle, $translations);
    $this->values = $values;
  }

  /**
   * @return array
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function get($field) {
    return $this->values[$field];
  }

  /**
   * {@inheritdoc}
   */
  public function set($name, $value, $notify = TRUE) {
    $this->values[$name] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return !empty($this->values['id']) ? $this->values['id'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    $this->entityTypeManager()->getStorage($this->entityTypeId)->delete([$this]);
  }

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = [];
    $fields[$entity_type->getKey('id')] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('ID'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    return $fields;
  }

}
