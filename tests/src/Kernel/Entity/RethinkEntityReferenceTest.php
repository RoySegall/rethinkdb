<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;
use Drupal\node\Entity\NodeType;
use Drupal\rethinkdb\RethinkDB;
use Drupal\Tests\rethinkdb\RethinkDBEntityReferenceSelectionTrait;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class RethinkEntityReferenceTest extends RethinkTestsBase {

  use RethinkDBEntityReferenceSelectionTrait;

  /**
   * {@inheritdoc}
   */
  protected $tables = ['rethinkdb_messages'];

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'rethinkdb',
    'rethinkdb_example',
    'node',
    'user',
    'system',
    'field',
  ];

  /**
   * The selection handler.
   *
   * @var \Drupal\rethinkdb\Plugin\EntityReferenceSelection\RethinkDBSelection
   */
  protected $selectionHandler;

  /**
   * List of array.
   *
   * @var array
   */
  protected $titles = [];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Install config.
    $this->installConfig(['rethinkdb']);

    /** @var RethinkDB $rethinkdb */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');
    $rethinkdb->createDb();

    if ($this->tables) {
      foreach ($this->tables as $table) {
        $rethinkdb->tableCreate($table);
      }
    }

    // Setting up the node type.
    NodeType::create([
      'type' => 'page',
      'name' => $this->randomString()
    ])->save();

    // Setting up an entity reference field.
    $this->createRethinkDBReferenceField(
      'node',
      'page',
      'field_message_reference',
      'Message reference',
      'rethinkdb_message',
      'rethinkdb',
      ['search_key' => 'title']
    );

    $this->selectionHandler = \Drupal::service('plugin.manager.entity_reference_selection')->createInstance('rethinkdb', [
      'handler' => 'rethink',
      'target_type' => 'rethinkdb_message',
      'handler_settings' => [
        'search_key' => 'title',
      ]
    ]);

    // Create two two entities.
    $this->titles = [
      'title 1',
      'title 2',
      'title 3',
    ];

    foreach ($this->titles as $title) {
      \Drupal::entityTypeManager()
        ->getStorage('rethinkdb_message')
        ->create(['title' => $title])
        ->save();
    }
  }

  /**
   * Verify we get form the selection handler the expected entities.
   */
  function testEntityReferenceField() {
    // Verify we get nothing with a title that don't exist.
    $results = $this->selectionHandler->getReferenceableEntities('foobar');
    $this->assertEquals($results, []);

    foreach ($this->titles as $delta => $title) {
      $results = $this->selectionHandler->getReferenceableEntities($title);
      $this->assertEquals(reset($results['rethinkdb_message']), $title);
    }
  }

}
