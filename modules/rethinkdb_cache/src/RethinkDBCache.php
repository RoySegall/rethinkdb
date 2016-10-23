<?php

namespace Drupal\rethinkdb_cache;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\CacheTagsChecksumInterface;
use Drupal\rethinkdb\RethinkDB;
use r\ValuedQuery\RVar;

/**
 * Implementing a cache layer based on RethinkDB storage.
 */
class RethinkDBCache implements CacheBackendInterface {

  /**
   * The name of the table to hold the cache data.
   *
   * @var string
   */
  protected $table_name = 'cache';

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
   * The DB connection.
   *
   * @var \r\Connection
   */
  protected $connection;

  /**
   * The cache tags checksum provider.
   *
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;

  /**
   * Constructing function.
   *
   * @param RethinkDB $rethinkdb
   *   RethinkDB service.
   * @param \Drupal\Core\Cache\CacheTagsChecksumInterface $checksum_provider
   *   The cache tags checksum provider.
   */
  function __construct(RethinkDB $rethinkdb, CacheTagsChecksumInterface $checksum_provider) {
    $this->rethinkdb = $rethinkdb;
    $this->table = $this->rethinkdb->getTable($this->table_name);
    $this->connection = $this->rethinkdb->getConnection();
    $this->checksumProvider = $checksum_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
    $cids = array($cid);
    $data = $this->getMultiple($cids, $allow_invalid);
    return reset($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {

    if (count($cids) === 1) {
      $documents = $this->table
        ->get(reset($cids))
        ->run($this->connection, ['cursor' => TRUE]);
    }
    else {
      $documents = $this->table
        ->getMultiple($cids)
        ->run($this->connection, ['cursor' => TRUE]);
    }

    $caches = [];

    if (!is_array($documents)) {
      return $caches;
    }

    while(list($key) = each($documents)) {
      $document = $documents[$key];
      if (!$this->validItem($document, $allow_invalid)) {
        continue;
      }

      $caches[$document->cid] = $document;
    }

    return $caches;
  }

  /**
   * Verifying the cache item is valid.
   *
   * @param $document
   *   The cache document.
   * @param $allow_invalid
   *   (optional) If TRUE, cache items may be returned even if they have expired
   *   or been invalidated. Such items may sometimes be preferred, if the
   *   alternative is recalculating the value stored in the cache, especially
   *   if another concurrent thread is already recalculating the same value. The
   *   "valid" property of the returned objects indicates whether the items are
   *   valid or not. Defaults to FALSE.
   *
   * @return bool
   */
  protected function validItem($document, $allow_invalid = FALSE) {
    if (!isset($document->data)) {
      return FALSE;
    }

    if (!$document->tags) {
      $document->tags = [];
    }

    // Check expire time.
    $document->valid = $document->expire == Cache::PERMANENT || $document->expire >= REQUEST_TIME;

    // Check if invalidateTags() has been called with any of the items's tags.
    // todo: find a way to prevent from query the mysql DB.
    if (!$this->checksumProvider->isValid($document->checksum, $document->tags)) {
       $document->valid = FALSE;
    }

    if (!$allow_invalid && !$document->valid) {
      return FALSE;
    }

    return $document;
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = Cache::PERMANENT, array $tags = array()) {

    $checksum = $this->checksumProvider->getCurrentChecksum($tags);

    if ($stored = $this->get($cid)) {
      // We already have the cache bin. Update the current one.
      $query = $this
        ->table
        ->get($stored->id)
        ->update([
          'data' => $data,
          'expire' => $expire,
          'tags' => $tags,
          'checksum' => $checksum,
        ]);
    }
    else {
      // This is a new bin. Creating a new one.
      $document = [
        'id' => $cid,
        'cid' => $cid,
        'data' => $data,
        'expire' => $expire,
        'tags' => $tags,
        'checksum' => $checksum,
      ];
      $query = $this->table->insert($document);
    }

    $query->run($this->connection);
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $items) {
    foreach ($items as $cid => $item) {
      $this->set($cid, $item['data'], isset($item['expire']) ? $item['expire'] : CacheBackendInterface::CACHE_PERMANENT, isset($item['tags']) ? $item['tags'] : array());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
    $cids = [$cid];
    $this->deleteMultiple($cids);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $cids) {
    $caches = $this->getMultiple($cids);
    foreach ($caches as $cache) {
      $this->table->get($cache->id)->delete()->run($this->connection);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    $this->table->delete()->run($this->connection);
  }

  /**
   * {@inheritdoc}
   */
  public function invalidate($cid) {
    $this->invalidateMultiple([$cid]);
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateMultiple(array $cids) {
    $caches = $this->getMultiple($cids);
    foreach ($caches as $cache) {
      $this->set($cache->cid, $cache->data, REQUEST_TIME - 1);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateAll() {
    $results = $this->table->run($this->connection)->toArray();

    $cids = [];
    foreach ($results as $result) {
      $cids[] = $result['cid'];
    }

    $this->invalidateMultiple($cids);
  }

  /**
   * {@inheritdoc}
   */
  public function garbageCollection() {
    $this->table->filter(function(RVar $doc) {
      return $doc->getField('expire')->ne(Cache::PERMANENT)->rAnd($doc->getField('expire')->lt(REQUEST_TIME));
    })->delete()->run($this->connection);
  }

  /**
   * {@inheritdoc}
   */
  public function removeBin() {
    $this->rethinkdb->tableDrop($this->table_name);
  }

  /**
   * RethinkDB cache layer add on - Creating the bin.
   */
  public function addBin() {
    $this->rethinkdb->tableCreate($this->table_name);
    $this->rethinkdb->createIndex($this->table_name, 'cid');
  }
}
