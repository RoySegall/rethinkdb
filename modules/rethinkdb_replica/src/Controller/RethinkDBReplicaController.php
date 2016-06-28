<?php

namespace Drupal\rethinkdb_replica\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Render\ElementInfoManager;
use Drupal\Core\Url;
use Drupal\rethinkdb\RethinkDB;
use Drupal\rethinkdb_replica\Entity\RethinkReplicaList;
use Drupal\rethinkdb_replica\RethinkDBReplica;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RethinkDBReplica.
 *
 * @package Drupal\rethinkdb_replica\Controller
 */
class RethinkDBReplicaController extends ControllerBase {

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
        $element = array(
          '#type' => 'operations',
          '#links' => array(
            'create_replica' => array(
              'title' => $this->t('Create a replica table'),
              'url' => Url::fromRoute('rethinkdb_replica.rethinkdb_replica_create', $params),
            ),
            'create_replica_and_clone' => array(
              'title' => $this->t('Create replica table and clone entities'),
              'url' => Url::fromRoute('rethinkdb_replica.rethinkdb_replica_create_and_clone', $params),
            ),
          ),
        );

        $operation = \Drupal::service('renderer')->render($element);
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
   * Creating a table replica and cloning all the entities of that type.
   *
   * @param $entity
   *   The enity type.
   */
  static public function createReplicaAndClone($entity) {
    // Get all the entities.

    // Split into small batches

    // Set batch operation.
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
    RethinkDBReplica::createReplica($entity);

    $params = ['@name' => \Drupal::entityTypeManager()->getDefinition($entity)->getLabel()];
    drupal_set_message($this->t('A replica table for @name has created.', $params));

    return new RedirectResponse(Url::fromRoute('rethinkdb_replica.rethinkdb_replica_list')->toString());
  }

}
