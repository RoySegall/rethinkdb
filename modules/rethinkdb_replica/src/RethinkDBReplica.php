<?php

namespace Drupal\rethinkdb_replica;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rethinkdb\RethinkDB;
use Drupal\rethinkdb_replica\Entity\RethinkReplicaList;

class RethinkDBReplica {

  /**
   * @return RethinkDBReplicaServices
   */
  public static function getService() {
    return \Drupal::service('rethinkdb_replica');
  }

}
