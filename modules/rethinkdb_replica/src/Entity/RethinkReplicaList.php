<?php

namespace Drupal\rethinkdb_replica\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Rethink replica list entity.
 *
 * @ConfigEntityType(
 *   id = "rethink_replica_list",
 *   label = @Translation("Rethink replica list"),
 *   handlers = {
 *     "list_builder" = "Drupal\rethinkdb_replica\RethinkReplicaListListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\rethinkdb_replica\RethinkReplicaListHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "rethink_replica_list",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class RethinkReplicaList extends ConfigEntityBase implements RethinkReplicaListInterface {

  /**
   * The Rethink replica list ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Rethink replica list label.
   *
   * @var string
   */
  protected $label;

}
