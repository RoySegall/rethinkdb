<?php

/**
 * @contains \Drupal\rethinkdb\Entity\AbstractRethinkDbEntity.
 */
namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Wrapping the base content entity class. This is used when we need to add
 * un-defined entity keys.
 *
 * When writing the object to RethinkDB
 */
abstract class AbstractRethinkDbEntity extends ContentEntityBase {

  /**
   * @var array
   */
  protected $dynamicFields = [];

  /**
   * @return array
   */
  public function getDynamicFields() {
    return $this->dynamicFields;
  }

  /**
   * @param array $dynamicFields
   *
   * @return $this
   */
  public function setDynamicFields($dynamicFields) {
    $this->dynamicFields = $dynamicFields;

    return $this;
  }

  /**
   * Adding a dynamic field on the fly.
   *
   * @param $key
   *   The name of the field.
   * @param $value
   *   The value of the field.
   *
   * @return $this
   */
  public function setDynamicField($key, $value) {
    $this->dynamicFields[$key] = $value;
    return $this;
  }

}
