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

    $replicas = RethinkReplicaList::loadMultiple();

    $rows = [];
    foreach (\Drupal::entityTypeManager()->getDefinitions() as $entity_type_id => $entity_type_info) {
      if (in_array($entity_type_id, array_keys($replicas))) {
        $operation = $this->t('Cloned');
      }
      else {
        $params = ['entity' => $entity_type_id];
        $operation = Link::createFromRoute($this->t('Create a replica table'), 'rethinkdb_replica.rethinkdb_replica_create', $params)->toString();
      }

      $rows[] = [
        $entity_type_info->getLabel(),
        $operation,
      ];
    }

    return [
      '#theme' => 'table',
      '#header' => [$this->t('Entity type'), $this->t('Operations')],
      '#rows' => $rows,
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
    RethinkReplicaList::create(['id' => $entity])->save();

    $params = ['@name' => \Drupal::entityTypeManager()->getDefinition($entity)->getLabel()];
    drupal_set_message($this->t('A replica table for @name has created.', $params));

    return new RedirectResponse(Url::fromRoute('rethinkdb_replica.rethinkdb_replica_list')->toString());
  }

}
