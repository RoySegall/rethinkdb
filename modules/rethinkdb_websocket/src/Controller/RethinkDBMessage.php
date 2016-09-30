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
   * Activity stream.
   *
   * @return string
   *   Return markup of the activity stream.
   */
  public function LiveChat() {
    return [
      '#type' => 'markup',
      '#markup' => "<div id='activity-stream'>
        <div class='header'>" . $this->t('Create a node or comment and the magic!') . "</div>
        <div class='content'></div>
      </div>",
      '#attached' => [
        'library' => ['rethinkdb_websocket/rethinkdb_activity_stream'],
        'drupalSettings' => [
          'rethinkdb_websocket' => [
            'app_id' => \Drupal::config('rethinkdb_websocket.settings')->get('pusher_app_id'),
          ]
        ],
      ],
    ];
  }

}
