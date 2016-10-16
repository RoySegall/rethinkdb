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
class RethinkCache extends RethinkTestsBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb', 'rethinkdb_cache'];

  /**
   * Testing cache workflow.
   */
  function testCache() {
    /** @var RethinkDBCache $cache */
    $cache = \Drupal::service('rethinkdb_cache');

    // Adding the bin.
    $cache->addBin();

    // Remove the bin.
    $cache->removeBin();
  }

}
