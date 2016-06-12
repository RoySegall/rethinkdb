<?php

/**
 * @file
 * Contains Drupal\rethinkdb\Entity\Query.
 */

namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\Query\QueryBase;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\rethinkdb\RethinkDB;
use r\Exceptions\RqlException;

class Query extends QueryBase implements QueryInterface {

  protected $operators = [
    '=' => 'eq',
    '!=' => 'ne',
    '>' => 'gt',
    '>=' => 'ge',
    '<' => 'lt',
    '<=' => 'le',
    'contain' => 'match',
  ];

  /**
   * {@inheritdoc}
   */
  public function execute() {
    /** @var \r\Queries\Tables\Table $table */
    $table = \r\table($this->entityType->getBaseTable());

    // Get conditions.
    $this
      ->addConditions($table)
      ->addPager();

    // Run over the items.
    return $this->getResults($table);
  }

  /**
   * Add conditions to the query.
   *
   * @param $table
   *   The table object.
   *
   * @return Query
   *
   * @throws RqlException
   */
  protected function addConditions(&$table) {
    foreach ($this->condition->conditions() as $condition) {
      $operator = !empty($condition['operator']) ? $condition['operator'] : '=';

      if (!in_array($operator, $this->operators)) {
        throw new RqlException("The operator {$operator} does not allowed. Only " . implode(', ', array_keys($this->operators)));
      }

      $row = \r\row($condition['field'])->{$this->operators[$operator]}($condition['value']);
      $table = $table->filter($row);
    }

    return $this;
  }

  /**
   * Adding pager to the query.
   *
   * @return $this
   */
  protected function addPager() {
    return $this;
  }

  protected function getResults($table) {
    /** @var RethinkDB $storage */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');

    $items = [];
    foreach ($table->run($rethinkdb->getConnection()) as $item) {
      $array_copy = $item->getArrayCopy();
      $items[$array_copy['id']] = $array_copy;
    }

    return $items;
  }

}
