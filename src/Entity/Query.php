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

  protected $filters = [
    '=' => 'eq',
    '!=' => 'ne',
    '>' => 'gt',
    '>=' => 'ge',
    '<' => 'lt',
    '<=' => 'le',
    'contains' => 'contains',
  ];

  /**
   * {@inheritdoc}
   */
  public function execute() {
    /** @var RethinkDB $storage */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');

    /** @var \r\Queries\Tables\Table $table */
    $table = \r\table($this->entityType->getBaseTable());

    // Get conditions.
    foreach ($this->condition->conditions() as $condition) {
      $operator = !empty($condition['operator']) ? $condition['operator'] : '=';
      $table = $table->filter(\r\row($condition['field'])->{$this->filters[$operator]}($condition['value']));
    }

    // Run over the items.
    $items = [];
    foreach ($table->run($rethinkdb->getConnection()) as $item) {
      $array_copy = $item->getArrayCopy();
      $items[$array_copy['id']] = $array_copy;
    }

    return $items;
  }

}
