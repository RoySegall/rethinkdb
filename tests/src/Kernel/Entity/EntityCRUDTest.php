<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;

use Drupal\Core\Site\Settings;
use Drupal\KernelTests\KernelTestBase;
use Drupal\rethinkdb_example\Entity\RethinkMessages;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class EntityCRUDTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb_example', 'rethinkdb'];

  function testTesting() {
    $messages = RethinkMessages::create(['title' => $this->randomString(), 'body' => $this->randomString()]);
    // Save it to the DB.
    $messages->save();
  }

}
