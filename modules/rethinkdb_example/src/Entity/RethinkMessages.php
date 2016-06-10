<?php

/**
 * @contains \Drupal\rethinkdb_example\Entity\RethinkMessages.
 */

namespace Drupal\rethinkdb_example\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Defines the node entity class.
 *
 * @ContentEntityType(
 *   id = "rethinkdb_message",
 *   label = @Translation("RethinkDB messages"),
 *   base_table = "rethinkdb_messages",
 *   translatable = TRUE,
 *   rethink = TRUE,
 *   entity_keys = {
 *     "id" = "mid",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "uid" = "uid",
 *   },
 * )
 */
class RethinkMessages extends ContentEntityBase {

  public $dynamicFields = [];

  public function setDynamicField($key, $value) {
    $this->dynamicFields[$key] = $value;
    return $this;
  }

}

