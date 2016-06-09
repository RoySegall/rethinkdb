<?php

/**
 * @file
 * Contains Drupal\rethinkdb_example\Controller\DefaultController.
 */

namespace Drupal\rethinkdb_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandler;
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
    $message = RethinkMessages::create([
      'title' => 'foo',
      'uid' => 1,
    ])->save();

    $results = $message->getArrayCopy();

    return [
      '#type' => 'markup',
      '#markup' => t('You entered a new record to the DB - @record', ['@record' => implode($results['generated_keys'])]),
    ];
  }

}
