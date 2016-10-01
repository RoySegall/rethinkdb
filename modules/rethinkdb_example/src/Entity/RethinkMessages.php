<?php

/**
 * @contains \Drupal\rethinkdb_example\Entity\RethinkMessages.
 */

namespace Drupal\rethinkdb_example\Entity;

use Drupal\rethinkdb\Entity\AbstractRethinkDbEntity;

/**
 * @ContentEntityType(
 *   id = "rethinkdb_message",
 *   label = @Translation("RethinkDB messages"),
 *   base_table = "rethinkdb_messages",
 *   translatable = FALSE,
 *   reference = "rethinkdb",
 *   entity_keys = {
 *    "id" = "id"
 *   },
 *   handlers = {
 *     "storage" = "Drupal\rethinkdb\RethinkStorage"
 *   }
 * )
 */
class RethinkMessages extends AbstractRethinkDbEntity {

}

