<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class EntityCRUDTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb_example'];

  function testTesting() {
    $this->assertEquals(1, 1);
  }

}
