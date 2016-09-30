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
 *   entity_keys = {
 *    "id" = "id"
 *   },
 *   handlers = {
 *     "storage" = "Drupal\rethinkdb\RethinkStorage"
 *   },
 *   reference_handler = "rethinkdb"
 * )
 */
class RethinkMessages extends AbstractRethinkDbEntity {

}

