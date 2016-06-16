<?php

/**
 * @contains \Drupal\rethinkdb\Entity\AbstractRethinkDbEntity.
 */
namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Wrapping the base content entity class. This is used when we need to add
 * un-defined entity keys.
 *
 * When writing the object to RethinkDB
 */
abstract class AbstractRethinkDbEntity extends ContentEntityBase {

  /**
   * @return array
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * @param array $dynamicFields
   *
   * @return $this
   */
  public function setValues($dynamicFields) {
    $this->values = $dynamicFields;

    return $this;
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
    return [];
  }

}
