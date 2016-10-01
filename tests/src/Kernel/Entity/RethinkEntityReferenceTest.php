<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb\Kernel\Entity\EntityCRUDTest.
 */

namespace Drupal\Tests\rethinkdb\Kernel\Entity;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\rethinkdb_websocket\Controller\RethinkDBMessage;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class RethinkEntityReferenceTest extends RethinkTestsBase {

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
    'field',
    'user',
    'system',
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

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installSchema('system', 'sequences');

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

  /**
   * Creates a field of RethinkDB entity reference field storage on the
   * specified bundle.
   *
   * @param string $entity_type
   *   The type of entity the field will be attached to.
   * @param string $bundle
   *   The bundle name of the entity the field will be attached to.
   * @param string $field_name
   *   The name of the field; if it already exists, a new instance of the existing
   *   field will be created.
   * @param string $field_label
   *   The label of the field.
   * @param string $target_entity_type
   *   The type of the referenced entity.
   * @param string $selection_handler
   *   The selection handler used by this field.
   * @param array $selection_handler_settings
   *   An array of settings supported by the selection handler specified above.
   *   (e.g. 'target_bundles', 'sort', 'auto_create', etc).
   * @param int $cardinality
   *   The cardinality of the field.
   *
   * @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\SelectionBase::buildConfigurationForm()
   */
  protected function createRethinkDBReferenceField($entity_type, $bundle, $field_name, $field_label, $target_entity_type, $selection_handler = 'default', $selection_handler_settings = array(), $cardinality = 1) {
    // Look for or add the specified field to the requested entity bundle.
    if (!FieldStorageConfig::loadByName($entity_type, $field_name)) {
      FieldStorageConfig::create(array(
        'field_name' => $field_name,
        'type' => 'rethinkdb',
        'entity_type' => $entity_type,
        'cardinality' => $cardinality,
        'settings' => array(
          'target_type' => $target_entity_type,
        ),
      ))->save();
    }
    if (!FieldConfig::loadByName($entity_type, $bundle, $field_name)) {
      FieldConfig::create(array(
        'field_name' => $field_name,
        'entity_type' => $entity_type,
        'bundle' => $bundle,
        'label' => $field_label,
        'settings' => array(
          'handler' => $selection_handler,
          'handler_settings' => $selection_handler_settings,
        ),
      ))->save();
    }
  }

}
