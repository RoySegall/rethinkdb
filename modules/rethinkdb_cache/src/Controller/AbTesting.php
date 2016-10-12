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

    for ($i = 0; $i <= 1000; $i++) {
      if ($operation == 'write') {
        $cache->set('testing' . 1, time());
      }
      else {
        $cache->get('testing' . 1, time());
      }
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    return [
      '#type' => 'markup',
      '#markup' => 'The action took ' . $time . ' for: ' . $database . ' to do ' . $operation,
    ];
  }

}
