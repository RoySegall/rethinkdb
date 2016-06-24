<?php

namespace Drupal\rethinkdb_replica\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RethinkDBReplica.
 *
 * @package Drupal\rethinkdb_replica\Routing
 * Listens to the dynamic route events.
 */
class RethinkDBReplica extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
  }
}
