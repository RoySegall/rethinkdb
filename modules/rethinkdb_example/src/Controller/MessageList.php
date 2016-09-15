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
    $mids = \Drupal::entityQuery('rethinkdb_message')
      ->execute();

    $messages = \Drupal::entityTypeManager()
      ->getStorage('rethinkdb_message')
      ->loadMultiple($mids);

    $list = [];
    foreach($messages as $id => $message) {
      $list[] = $this->t('@id: @title - @body', [
        '@id' => $message->values['id'],
        '@title' => $message->values['title'],
        '@body' => $message->values['body'],
      ]);
    }

    return [
      '#theme' => 'item_list',
      '#items' =>  $list,
    ];
  }

}
