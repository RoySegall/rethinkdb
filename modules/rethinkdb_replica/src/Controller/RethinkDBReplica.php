<?php

namespace Drupal\rethinkdb_replica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\rethinkdb\RethinkDB;
use Drupal\rethinkdb_replica\Entity\RethinkReplicaList;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RethinkDBReplica.
 *
 * @package Drupal\rethinkdb_replica\Controller
 */
class RethinkDBReplica extends ControllerBase {

  /**
   * Manage entities replica creation.
   *
   * @return string
   *   Manageable replica list of entities.
   */
  public function replicaList() {
    return [
      '#type' => 'item_list',
      '#markup' => Link::createFromRoute('Node', 'rethinkdb_replica.rethinkdb_replica_create', ['entity' => 'node'])->toString(),
    ];
  }

  /**
   * Create a replica of an entity type.
   * 
   * @param $entity
   *   The entity type iD.
   * 
   * @return RedirectResponse
   */
  public function createReplica($entity) {
    /** @var RethinkDB $rethink */
    $rethink = \Drupal::service('rethinkdb');
    $rethink->tableCreate($entity . '_replica');
    RethinkReplicaList::create(['id' => 'node'])->save();
    return new RedirectResponse(Url::fromRoute('rethinkdb_replica.rethinkdb_replica_list')->toString());
  }

}
