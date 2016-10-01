<?php

namespace Drupal\rethinkdb_cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\rethinkdb\RethinkDB;

/**
 * Implementing a cache layer based on RethinkDB storage.
 */
class RethinkDBCache implements CacheBackendInterface {

  /**
   * Get the cache service.
   *
   * @return \Drupal\rethinkdb_cache\RethinkDBCache
   *   The cache service object.
   */
  static public function getService() {
    return \Drupal::service('rethinkdb_cache');
  }

  /**
   * @var RethinkDB
   */
  protected $rethinkdb;

  /**
   * Constructing function.
   *
   * @param RethinkDB $rethinkdb
   *   RethinkDB service.
   */
  function __construct(RethinkDB $rethinkdb) {
    $this->rethinkdb = $rethinkdb;
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = Cache::PERMANENT, array $tags = array()) {
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $items) {
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $cids) {
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
  }

  /**
   * {@inheritdoc}
   */
  public function invalidate($cid) {
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateMultiple(array $cids) {
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateAll() {
  }

  /**
   * {@inheritdoc}
   */
  public function garbageCollection() {
  }

  /**
   * {@inheritdoc}
   */
  public function removeBin() {
  }
}
