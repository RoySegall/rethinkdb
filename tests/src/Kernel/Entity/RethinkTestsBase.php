<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\RethinkTestsBase.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;

use Drupal\KernelTests\KernelTestBase;
use Drupal\rethinkdb\RethinkDB;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class RethinkTestsBase extends KernelTestBase {

  protected $table = [];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    /** @var RethinkDB $rethinkdb */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');
    $rethinkdb->createDb();

    if ($this->tables) {
      foreach ($this->tables as $table) {
        $rethinkdb->tableCreate($table);
      }
    }
  }

}
