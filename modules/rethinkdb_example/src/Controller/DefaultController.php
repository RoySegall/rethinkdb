<?php

/**
 * @file
 * Contains Drupal\rethinkdb_example\Controller\DefaultController.
 */

namespace Drupal\rethinkdb_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rethinkdb_example\Entity\RethinkMessages;

/**
 * Class DefaultController.
 *
 * @package Drupal\rethinkdb_example\Controller
 */
class DefaultController extends ControllerBase {
  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {

    // Get the first message for testing.
    $message = RethinkMessages::load(1);

    if (!$message) {
      $message = RethinkMessages::create([
        'title' => 'foo',
        'uid' => 1,
      ])->save();
    }

    return [
      '#type' => 'markup',
      '#markup' => $message,
    ];
  }

}
