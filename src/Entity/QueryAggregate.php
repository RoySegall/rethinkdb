<?php

/**
 * @file
 * Contains \Drupal\mongodb\Entity\QueryAggregate.
 */

namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\Query\ConditionInterface;
use Drupal\Core\Entity\Query\QueryAggregateInterface;

class QueryAggregate extends Query implements QueryAggregateInterface {

  /**
   * Queries for the existence of a field.
   *
   * @param string $field
   *   The name of the field.
   * @param string $function
   *   The aggregate function.
   * @param $langcode
   *   (optional) The language code.
   *
   * @return \Drupal\Core\Entity\Query\QueryAggregateInterface
   *   The called object.
   */
  public function existsAggregate($field, $function, $langcode = NULL) {
    return $this;
  }

  /**
   * Queries for the nonexistence of a field.
   *
   * @param string $field .
   *   The name of a field.
   * @param string $function
   *   The aggregate function.
   * @param string $langcode
   *   (optional) The language code.
   *
   * @return \Drupal\Core\Entity\Query\QueryAggregateInterface
   *   The called object.
   */
  public function notExistsAggregate($field, $function, $langcode = NULL) {
    return $this;
  }

  /**
   * Creates an object holding a group of conditions.
   *
   * See andConditionAggregateGroup() and orConditionAggregateGroup() for more.
   *
   * @param string $conjunction
   *   - AND (default): this is the equivalent of andConditionAggregateGroup().
   *   - OR: this is the equivalent of andConditionAggregateGroup().
   *
   * @return ConditionInterface
   *   An object holding a group of conditions.
   */
  public function conditionAggregateGroupFactory($conjunction = 'AND') {
  }

}
