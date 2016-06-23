<?php

/**
 * @contains \Drupal\rethinkdb_example\Entity\RethinkMessages.
 */

namespace Drupal\rethinkdb_example\Entity;

use Drupal\rethinkdb\Entity\AbstractRethinkDbEntity;

/**
 * Defines the node entity class.
 *
 * @ContentEntityType(
 *   id = "rethinkdb_message",
 *   label = @Translation("RethinkDB messages"),
 *   base_table = "rethinkdb_messages",
 *   translatable = FALSE,
 *   rethink = TRUE,
 *   entity_keys = {
 *    "id" = "id"
 *   }
 * )
 */
class RethinkMessages extends AbstractRethinkDbEntity {

}

