<?php

/**
 * @contains \Drupal\rethinkdb\RethinkStorage.
 */

namespace Drupal\rethinkdb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Serializer;

class RethinkStorage extends SqlContentEntityStorage implements EntityStorageInterface {

  /**
   * @var Serializer
   */
  protected $serializer;

  /**
   * @var RethinkDB
   */
  protected $rethinkdb;

  /**
   * Constructs a SqlContentEntityStorage object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend to be used.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The serializer manager.
   * @param RethinkDB $rethinkdb
   *   The rethinkDB service.
   */
  public function __construct(EntityTypeInterface $entity_type, Connection $database, EntityManagerInterface $entity_manager, CacheBackendInterface $cache, LanguageManagerInterface $language_manager, Serializer $serializer, RethinkDB $rethinkdb) {
    parent::__construct($entity_type, $database, $entity_manager, $cache, $language_manager);
    $this->serializer = $serializer;
    $this->rethinkdb = $rethinkdb;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('database'),
      $container->get('entity.manager'),
      $container->get('cache.entity'),
      $container->get('language_manager'),
      $container->get('serializer'),
      $container->get('rethinkdb')
    );
  }

  /**
   * Get the base table easilly.
   *
   * @return null|string
   */
  protected function getTableName() {
    return $this->entityManager->getDefinition($this->entityTypeId)
      ->getBaseTable();
  }

  /**
   * {@inheritdoc}
   */
  public function onEntityTypeCreate(EntityTypeInterface $entity_type) {
    // Creating the table in the RethinkDB DB.
    $this->rethinkdb->tableCreate($this->getTableName());
  }

  /**
   * Performs storage-specific loading of entities.
   *
   * Override this method to add custom functionality directly after loading.
   * This is always called, while self::postLoad() is only called when there are
   * actual results.
   *
   * @param array|null $ids
   *   (optional) An array of entity IDs, or NULL to load all entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Associative array of entities, keyed on the entity ID.
   */
  protected function doLoadMultiple(array $ids = NULL) {
  }

  /**
   * Determines if this entity already exists in storage.
   *
   * @param int|string $id
   *   The original entity ID.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being saved.
   *
   * @return bool
   */
  protected function has($id, EntityInterface $entity) {
  }

  /**
   * Performs storage-specific entity deletion.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entity objects to delete.
   */
  protected function doDelete($entities) {
  }

  /**
   * Performs storage-specific saving of the entity.
   *
   * @param int|string $id
   *   The original entity ID.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to save.
   *
   * @return bool|int
   *   If the record insert or update failed, returns FALSE. If it succeeded,
   *   returns SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   */
  protected function doSave($id, EntityInterface $entity) {
    $values = array_map(function($item) {
      return $item->value;
    }, $entity->getFields());

    return $this->rethinkdb->insert($this->getTableName(), $values);
  }

  /**
   * {@inheritdoc}
   */
  protected function getQueryServiceName() {
    return 'entity.query.rethink_db';
  }

  /**
   * Load a specific entity revision.
   *
   * @param int|string $revision_id
   *   The revision id.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The specified entity revision or NULL if not found.
   */
  public function loadRevision($revision_id) {
  }

  /**
   * Delete a specific entity revision.
   *
   * A revision can only be deleted if it's not the currently active one.
   *
   * @param int $revision_id
   *   The revision id.
   */
  public function deleteRevision($revision_id) {
  }

}
