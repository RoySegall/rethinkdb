<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;

use Drupal\rethinkdb_example\Entity\RethinkMessages;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class EntityCRUDTest extends RethinkTestsBase {

  /**
   * {@inheritdoc}
   */
  protected $tables = ['rethinkdb_messages'];

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb', 'rethinkdb_example'];

  /**
   * Testing the basic CRUD operations.
   */
  function testCrud() {
    $message = RethinkMessages::create(['title' => $this->randomString(), 'body' => $this->randomString()]);
    // Save it to the DB.
    $results = $message->save();

    // Checking we got the correct the document ID.
    $this->assertNotEmpty($results['generated_keys']);

    // Load the document from DB.
    $document = RethinkMessages::load(reset($results['generated_keys']));

    $this->assertEquals($document->get('title'), $message->get('title'));
    $this->assertEquals($document->get('body'), $message->get('body'));

    // Update the document.
    $document->set('title', 'new title')->save();

    $document = RethinkMessages::load(reset($results['generated_keys']));
    $this->assertNotEquals($document->get('title'), $message->get('title'));

    // Delete the document.
    $document->delete();

    $this->assertEmpty(RethinkMessages::load(reset($results['generated_keys'])));
  }

}
