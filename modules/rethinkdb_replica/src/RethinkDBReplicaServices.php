<?php

namespace Drupal\rethinkdb_replica;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\rethinkdb\RethinkDB;
use Symfony\Component\Serializer\Serializer;

class RethinkDBReplicaServices {

  /**
   * @var RethinkDB
   */
  protected $rethinkdb;

  /**
   * @var EntityTypeManager
   */
  protected $entityManger;

  function __construct(RethinkDB $rethinkdb, EntityTypeManager $entity_manager) {
    $this->rethinkdb = $rethinkdb;
    $this->entityManger = $entity_manager;
  }

  /**
   * Creating a replica of the DB.
   *
   * @param $entity_type_id
   */
  public function createReplica($entity_type_id) {
    $this->rethinkdb->tableCreate($entity_type_id . '_replica');
    $this->entityManger->getStorage('rethink_replica_list')->create(['id' => $entity_type_id])->save();
  }

  /**
   * Flattering the entity object and make it ready for storing in RethinkDB.
   *
   * @param EntityInterface $entity
   *   The entity object.
   *
   * @return array
   */
  public function EntityFlatter(EntityInterface $entity) {
    /** @var Serializer $serializer */
    $serializer = \Drupal::service('serializer');

    $normalize = $serializer->normalize($entity, 'json');

    foreach ($normalize as &$item) {
      if (is_array($item)) {
        $flatt_key = reset($item);
        $item = is_array($flatt_key) ? reset($flatt_key) : $flatt_key;
      }
    }

    return $normalize;
  }

}
