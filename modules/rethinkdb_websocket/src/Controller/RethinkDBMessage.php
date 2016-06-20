<?php

/**
 * @file
 * Contains Drupal\rethinkdb_websocket\Controller\RethinkDBMessage.
 */

namespace Drupal\rethinkdb_websocket\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class RethinkDBMessage.
 *
 * @package Drupal\rethinkdb_websocket\Controller
 */
class RethinkDBMessage extends ControllerBase {

  /**
   * Livechat.
   *
   * @return string
   *   Return Hello string.
   */
  public function LiveChat() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: LiveChat')
    ];
  }

}
