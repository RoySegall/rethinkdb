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
  public function AbTestingController($database, $operation, $items) {

    $cache = $database == 'rethinkdb' ? \Drupal::service('rethinkdb_cache') : \Drupal::cache();
    $time_start = microtime(true);

    if ($operation == 'write') {
      for ($i = 0; $i <= $items; $i++) {
        $cache->set('testing' . $i, time());
      }
    }
    else {

      $caches = [];
      for ($i = 0; $i <= $items; $i++) {
        $caches[] = 'testing' . $i;
      }

      $foo = $cache->getMultiple($caches);
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;
    dpm(count($foo));

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
