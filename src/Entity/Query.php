<?php

/**
 * @file
 * Contains Drupal\rethinkdb\Entity\Query.
 */

namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\Query\QueryBase;
use Drupal\Core\Entity\Query\QueryInterface;

class Query extends QueryBase implements QueryInterface {

  /**
   * {@inheritdoc}
   */
  public function execute() {
  }

}
