<?php

namespace Drupal\rethinkdb_cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\rethinkdb\RethinkDB;

/**
 * Implementing a cache layer based on RethinkDB storage.
 */
class RethinkDBCache implements CacheBackendInterface {

  const TABLE = 'cache';

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
   * The table object.
   *
   * @var \r\Queries\Tables\Table
   */
  protected $table;

  /**
   * Constructing function.
   *
   * @param RethinkDB $rethinkdb
   *   RethinkDB service.
   */
  function __construct(RethinkDB $rethinkdb) {
    $this->rethinkdb = $rethinkdb;
    $this->table = $this->rethinkdb->getTable(RethinkDBCache::TABLE);
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
    // Query the DB for the cached data.
    $row = \r\row('cid')->eq($cid);
    $data = $this->table
      ->filter($row)
      ->run($this->rethinkdb->getConnection())
      ->toArray();

    if (!$data) {
      return;
    }

    return $data[0];
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
    $document = [
      'cid' => $cid,
      'data' => $data,
      'expire' => $expire,
      'tags' => $tags,
    ];
    if ($stored = $this->get($cid)) {
      $query = $this->table->get($cid)->update($document);
    }
    else {
      $query = $this->table->insert($document);
    }
    $query->run($this->rethinkdb->getConnection());
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
