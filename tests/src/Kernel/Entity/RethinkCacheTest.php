<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;
use Drupal\rethinkdb_cache\RethinkDBCache;

/**
 * Testing the cache layer functionality.
 *
 * @group rethinkdb_drupal
 */
class RethinkCacheTest extends RethinkTestsBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb', 'rethinkdb_cache'];

  /**
   * The cache service.
   *
   * @var \Drupal\rethinkdb_cache\RethinkDBCache
   */
  protected $cache;

  protected $cid;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::SetUp();
    $this->cache = RethinkDBCache::getService();

    // Adding the bin.
    $this->cache->addBin();
  }

  /**
   * Testing cache workflow.
   */
  function testCache() {
    $cid = $this->randomString();
    $data = $this->randomString();

    // Sleep and wait for the secondary index to be ready.
    sleep(5);

    // Get the cache and expect for NULL.
    $this->assertEmpty($this->cache->get($cid));

    // Set a single cache.
    $this->cache->set($cid, $data);

    // Get the single cache.
    $this->assertEquals($this->cache->get($cid)->data, $data);

    // invalidate the single cache.
    $this->cache->invalidate($cid);

    // Verify valid and in valid.
    $this->assertEmpty($this->cache->get($cid));
    $this->assertNotEmpty($this->cache->get($cid, TRUE));

    // Delete all the cache.
    $this->cache->deleteAll();

    // Verify we can't get nothing. Even non valid cache.
    $this->assertEmpty($this->cache->get($cid));
    $this->assertEmpty($this->cache->get($cid, TRUE));

    // Remove the bin.
    $this->cache->removeBin();

    try {
      $this->cache->get($cid);
      $this->fail('The bin was loaded although it removed.');
    }
    catch (\Exception $e) {
      $this->assertTrue(TRUE, 'The bin waa removed and cannot be used.');
    }
  }

}
