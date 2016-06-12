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
use r\Queries\Tables\Table;

class Query extends QueryBase implements QueryInterface {

  /**
   * @var array
   *
   * Keep the allowed operators on the query.
   */
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
   * @var Table
   *
   * The query object.
   */
  protected $table;

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $this->table = \r\table($this->entityType->getBaseTable());

    return $this
      ->addConditions()
      ->addPager()
      ->getResults();
  }

  /**
   * Add conditions to the query.
   *
   * @return Query
   *
   * @throws RqlException
   */
  protected function addConditions() {
    foreach ($this->condition->conditions() as $condition) {
      $operator = !empty($condition['operator']) ? $condition['operator'] : '=';

      if (!in_array($operator, array_keys($this->operators))) {
        throw new RqlException("The operator {$operator} does not allowed. Only " . implode(', ', array_keys($this->operators)));
      }

      $row = \r\row($condition['field'])->{$this->operators[$operator]}($condition['value']);
      $this->table = $this->table->filter($row);
    }

    return $this;
  }

  /**
   * Adding pager to the query.
   *
   * @return $this
   */
  protected function addPager() {
    if ($this->range) {
      $this->table = $this->table->slice($this->range['start'], $this->range['length']);
    }

    return $this;
  }

  /**
   * Return the results of the query.
   *
   * @return array
   */
  protected function getResults() {
    /** @var RethinkDB $storage */
    $rethinkdb = \Drupal::getContainer()->get('rethinkdb');

    $items = [];
    foreach ($this->table->run($rethinkdb->getConnection()) as $item) {
      $array_copy = $item->getArrayCopy();
      $items[$array_copy['id']] = $array_copy;
    }

    return $items;
  }

}
