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
    $messages = \Drupal::entityQuery('rethinkdb_message')
      ->condition('title', 't', 'contain')
      ->execute();

    $list = [];
    foreach($messages as $id => $message) {
      $list[] = $this->t('@id: @title - @body', [
        '@id' => $message['id'],
        '@title' => $message['title'],
        '@body' => $message['body']
      ]);
    }

    return [
      '#theme' => 'item_list',
      '#items' =>  $list,
    ];
  }

}
