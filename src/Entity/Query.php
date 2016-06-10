<?php

/**
 * @file
 * Contains Drupal\rethinkdb\Entity\Query.
 */

namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\Query\QueryBase;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\rethinkdb\RethinkDB;

class Query extends QueryBase implements QueryInterface {

  /**
   * {@inheritdoc}
   */
  public function execute() {
    /** @var RethinkDB $storage */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');

    $table = \r\table($this->entityType->getBaseTable());

    // Get conditions.

    // Run over the items.
    $items = [];
    foreach ($table->run($rethinkdb->getConnection()) as $item) {
      $items[] = $item->getArrayCopy();
    }

    return $items;
  }

}
