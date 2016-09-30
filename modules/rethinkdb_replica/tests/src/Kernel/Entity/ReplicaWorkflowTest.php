<?php

/**
 * @file
 * Contains Drupal\Tests\rethinkdb_replica\Kernel\Entity\ReplicaWorkflowTest.
 */

namespace Drupal\Tests\rethinkdb_replica\Kernel\Entity;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\rethinkdb\RethinkDB;
use Drupal\rethinkdb_replica\RethinkDBReplica;
use Drupal\Tests\rethinkdb\Kernel\Entity\RethinkTestsBase;
use r\Queries\Control\Changes;

/**
 * Tests entity reference selection plugins.
 *
 * @group rethinkdb_drupal
 */
class ReplicaWorkflowTest extends RethinkTestsBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['rethinkdb_replica', 'rethinkdb', 'entity_test', 'user', 'serialization'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('entity_test');
    $this->installEntitySchema('user');
  }

  /**
   * Testing the workflow of replicating tables.
   */
  function testWorkflow() {
    RethinkDBReplica::getService()->createReplica('entity_test');

    // Creating an entity.
    $label = $this->randomString();
    $entity = EntityTest::create([
      'label' => $label
    ]);
    $entity->save();
    
    // Verify the replica created.
    $entity_definition = \Drupal::entityTypeManager()
      ->getDefinition($entity->getEntityTypeId());

    $primary_key = $entity_definition->getKey('id');

    $rethink = RethinkDb::getService();
    $replica_name = $entity->getEntityTypeId() . '_replica';

    $result = $rethink->getTable($replica_name)
      ->filter(\r\row($primary_key)->eq($entity->id()))
      ->run($rethink->getConnection());

    $document = $result->current()->getArrayCopy();

    $this->assertNotEmpty($document);

    // Updating the dummy entity.
    $new_label = $this->randomString();
    $entity->set('name', $new_label);
    $entity->save();

    $result = $rethink->getTable($replica_name)
      ->filter(\r\row($primary_key)->eq($entity->id()))
      ->run($rethink->getConnection());

    $document = $result->current()->getArrayCopy();

    $this->assertEquals($document['name'], $new_label);
    $this->assertNotEquals($document['name'], $label);

    // Deleting the entity.
    $entity->delete();

    $result = $rethink->getTable($replica_name)
      ->get($document['id'])
      ->run($rethink->getConnection());

    $this->assertEmpty($result);
  }

}
