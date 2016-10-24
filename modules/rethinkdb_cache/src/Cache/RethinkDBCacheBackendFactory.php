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
class RethinkDBCacheBackendFactory implements CacheFactoryInterface {

  /**
   * @var \Drupal\rethinkdb_cache\RethinkDBCache
   */
  protected $clientFactory;

  /**
   * The cache tags checksum provider.
   *
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;

  /**
   * List of cache bins.
   *
   * Renderer and possibly other places fetch backends directly from the
   * factory. Avoid that the backend objects have to fetch meta information like
   * the last delete all timestamp multiple times.
   *
   * @var array
   */
  protected $bins = [];

  /**
   * Creates a rethinkdb cache CacheBackendFactory.
   *
   * @param RethinkDBCache $client_factory
   * @param CacheTagsChecksumInterface $checksum_provider
   */
  function __construct(RethinkDBCache $client_factory, CacheTagsChecksumInterface $checksum_provider) {
    $this->clientFactory = $client_factory;
    $this->checksumProvider = $checksum_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $cache = clone $this->clientFactory;

    // todo: create the table bin.
    $cache->setTableName($bin);

    return $cache;
  }

}
