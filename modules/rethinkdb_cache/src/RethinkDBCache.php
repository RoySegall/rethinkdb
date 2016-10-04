<?php

namespace Drupal\rethinkdb_cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\rethinkdb\RethinkDB;
use r\ValuedQuery\RVar;

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

    $cids = array($cid);
    $data = $this->getMultiple($cids, $allow_invalid);

    if (!$data) {
      return;
    }

    return reset($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
    $documents = $this->table
      ->filter(function(RVar $doc) use ($cids) {
        return \r\expr($cids)->contains($doc->getField('cid'));
      })
      ->run($this->rethinkdb->getConnection());

    $caches = [];

    foreach ($documents as $document) {
      $array_copy = $document->getArrayCopy();
      $caches[$array_copy['cid']] = (object)$array_copy;
    }

    return $caches;
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = Cache::PERMANENT, array $tags = array()) {

    if ($stored = $this->get($cid)) {
      // We already have the cache bin. Update the current one.
      $query = $this
        ->table
        ->get($stored->id)
        ->update(['data' => $data]);
    }
    else {
      // This is a new bin. Creating a new one.
      $document = [
        'cid' => $cid,
        'data' => $data,
        'expire' => $expire,
        'tags' => $tags,
      ];
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
