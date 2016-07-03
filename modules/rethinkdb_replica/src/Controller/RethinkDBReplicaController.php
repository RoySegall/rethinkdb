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
      $params = ['entity' => $entity_type_id];

      if (in_array($entity_type_id, array_keys($replicas))) {
        $links = [
          'create_replica_and_clone' => [
            'title' => $this->t('Clone entities'),
            'url' => Url::fromRoute('rethinkdb_replica.rethinkdb_replica_create_and_clone', $params),
          ],
        ];
      }
      else {
        $links = [
          'create_replica' => [
            'title' => $this->t('Create a replica table'),
            'url' => Url::fromRoute('rethinkdb_replica.rethinkdb_replica_create', $params),
          ],
          'create_replica_and_clone' => [
            'title' => $this->t('Create replica table and clone entities'),
            'url' => Url::fromRoute('rethinkdb_replica.rethinkdb_replica_create_and_clone', $params),
          ],
        ];
      }

      $element = array(
        '#type' => 'operations',
        '#links' => $links,
      );

      $rows[] = [
        $entity_type_info->getLabel(),
        \Drupal::service('renderer')->render($element),
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
   *   The entity type.
   *
   * @return array
   */
  static public function createReplicaAndClone($entity) {
    // Get all the entities.
    $entities = \Drupal::entityQuery($entity)
      ->execute();

    // Split into small batches and setting the operations.
    $chunks = array_chunk(array_keys($entities), 150);

    $operations = [];

    if (!RethinkReplicaList::load($entity)) {
      $operations[] = [[self::class, 'createDbReplica'], [$entity]];
    }

    foreach ($chunks as $chunk) {
      $operations[] = [[self::class, 'copyEntity'], [$entity, $chunk]];
    }

    // Start the batch operation.
    $batch = array(
      'title' => t('Creating replica'),
      'operations' => $operations,
    );
    batch_set($batch);

    return batch_process(Url::fromRoute('rethinkdb_replica.rethinkdb_replica_list'));
  }

  /**
   * Creating a replica of the table.
   *
   * @param $entity
   *   The entity type.
   */
  public static function createDbReplica($entity) {
    RethinkDBReplica::getService()->createReplica($entity);
  }

  /**
   * @param $entity
   *   The entity type.
   * @param $ids
   *   The entity IDs
   */
  public static function copyEntity($entity, $ids) {
    $entities = \Drupal::entityTypeManager()->getStorage($entity)->loadMultiple($ids);

    foreach ($entities as $entity) {
      $document = RethinkDBReplica::getService()->EntityFlatter($entity);
      RethinkDb::getService()->insert($entity->getEntityTypeId() . '_replica', $document);
    }
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
    RethinkDBReplica::getService()->createReplica($entity);

    $params = ['@name' => \Drupal::entityTypeManager()->getDefinition($entity)->getLabel()];
    drupal_set_message($this->t('A replica table for @name has created.', $params));

    return new RedirectResponse(Url::fromRoute('rethinkdb_replica.rethinkdb_replica_list')->toString());
  }

}
