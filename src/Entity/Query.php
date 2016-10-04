<?php

/**
 * @file
 * Contains Drupal\rethinkdb\Entity\Query.
 */

namespace Drupal\rethinkdb\Entity;

use Dflydev\PlaceholderResolver\DataSource\ArrayDataSourceTest;
use Drupal\Core\Entity\Query\QueryBase;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\rethinkdb\RethinkDB;
use r\Datum\ArrayDatum;
use r\Exceptions\RqlException;
use r\Queries\Tables\Table;
use r\ValuedQuery\RVar;

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
    'CONTAINS' => 'match',
    'IN' => 'args',
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
      ->addOrderBy()
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

      if ($operator == 'IN') {
        $row = function(RVar $doc) use ($condition) {
          return \r\expr($condition['value'])->contains($doc->getField($condition['field']));
        };
      }
      else {
        $row = \r\row($condition['field'])->{$this->operators[$operator]}($condition['value']);
      }
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
   * Ordering the query by a given key.
   *
   * @return Query
   */
  protected function addOrderBy() {
    foreach ($this->sort as $sort) {
      $sort['field'] = empty($sort['field']) ? 'id' : $sort['field'];
      $sort_object = $sort['direction'] == 'ASC' ? \r\Asc($sort['field']) : \r\Desc($sort['field']);
      $this->table = $this->table->orderBy($sort_object);
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
      $items[] = $array_copy['id'];
    }

    return $items;
  }

}
