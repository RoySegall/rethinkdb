<?php

namespace Drupal\rethinkdb_replica;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rethinkdb\RethinkDB;
use Drupal\rethinkdb_replica\Entity\RethinkReplicaList;

class RethinkDBReplica {

  /**
   * Flattering the entity object and make it ready for storing in RethinkDB.
   *
   * @param EntityInterface $entity
   *   The entity object.
   *
   * @return array
   */
  public static function EntityFlatter(EntityInterface $entity) {
    $entity_array = $entity->toArray();

    foreach ($entity_array as $key => $field) {
      if (count($field) == 1) {
        // Single field. Flatten the array.
        if ($key == 'body') {
          // This is a body field. No need for other values.
          $new_value = $field[0]['value'];
        }
        else {
          $new_value = reset($field[0]);
        }
      }
      else {
        // A field with multiple cardinality. Flat that array as well.
        // No need for recursion since there is no more than two level in this
        // case.
        $new_value = [];
        foreach ($field as $value) {
          $new_value[] = reset($value);
        }
      }

      $entity_array[$key] = $new_value;
    }

    return $entity_array;
  }

  /**
   * Creating a replica of the DB.
   * 
   * @param $entity_type_id
   */
  public static function createReplica($entity_type_id) {
    /** @var RethinkDB $rethink */
    $rethink = \Drupal::service('rethinkdb');
    $rethink->tableCreate($entity_type_id . '_replica');
    RethinkReplicaList::create(['id' => $entity_type_id])->save();
  }

}
