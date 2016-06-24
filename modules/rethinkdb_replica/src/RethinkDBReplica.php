<?php

namespace Drupal\rethinkdb_replica;

use Drupal\Core\Entity\EntityInterface;

class RethinkDBReplica {

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
  
}
