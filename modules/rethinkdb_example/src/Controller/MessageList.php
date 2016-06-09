<?php

/**
 * @file
 * Contains Drupal\rethinkdb_example\Controller\MessageList.
 */

namespace Drupal\rethinkdb_example\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class MessageList.
 *
 * @package Drupal\rethinkdb_example\Controller
 */
class MessageList extends ControllerBase {

  /**
   * Message list.
   *
   * @return string
   *   Return Hello string.
   */
  public function MessageList() {
    $foo = \Drupal::entityQuery('rethinkdb_message');

    dpm($foo);

    return [
        '#type' => 'markup',
        '#markup' => $this->t('Implement method: list')
    ];
  }

}
