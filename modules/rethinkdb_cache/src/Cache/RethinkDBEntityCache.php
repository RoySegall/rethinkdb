<?php

/**
 * @file
 * Contains \Drupal\rethinkdb_cache\Cache\CacheBackendFactory.
 */

namespace Drupal\rethinkdb_cache\Cache;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\CacheTagsChecksumInterface;
use Drupal\rethinkdb_cache\RethinkDBCache;

/**
 * A generic cache factory based on RethinkDB cache.
 */
class RethinkDBEntityCache extends RethinkDBCacheBackendFactory {

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $cache = clone $this->clientFactory;

    // todo: create the table bin.
    $cache->setTableName('entity_cache');

    return $cache;
  }

}
