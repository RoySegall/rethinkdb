<?php

namespace Drupal\rethinkdb_cache\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AbTesting.
 *
 * @package Drupal\rethinkdb_cache\Controller
 */
class AbTesting extends ControllerBase {

  /**
   * Checking how much time would it to read/write 1,000 records from the cache
   * backend.
   *
   * @param $database
   *   The type of the database - rethinkdb or mysql
   * @param $operation
   *   The type of the operation - read or write.
   *
   * @return string
   *   The amount of duration for the operation.
   */
  public function AbTestingController($database, $operation) {

    $cache = $database == 'rethinkdb' ? \Drupal::service('rethinkdb_cache') : \Drupal::cache();
    $time_start = microtime(true);

    if ($operation == 'write') {
      for ($i = 0; $i <= 1000; $i++) {
        $cache->set('testing' . $i, time());
      }
    }
    else {
      $cids = [];

      for ($i = 0; $i <= 1000; $i++) {
        $cids[] = 'testing' . $i;
      }

      $cache->getMultiple($cids);
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    $params = [
      '@time' => $time,
      '@database' => $database,
      '@operation' => $operation,
    ];
    return [
      '#type' => 'markup',
      '#markup' => $this->t('The action took @time for: @database to do @operation', $params),
    ];
  }

}
