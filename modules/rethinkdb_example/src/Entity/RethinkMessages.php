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
 *   handlers = {
 *     "storage" = "Drupal\rethinkdb\RethinkStorage"
 *   },
 *   entity_keys = {}
 * )
 */
class RethinkMessages extends AbstractRethinkDbEntity {

}

