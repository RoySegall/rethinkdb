<?php

/**
 * @file
 * Contains Drupal\rethinkdb_example\Controller\DefaultController.
 */

namespace Drupal\rethinkdb_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DefaultController.
 *
 * @package Drupal\rethinkdb_example\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * @var PrivateTempStoreFactory
   */
  protected $tempStore;

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivateTempStoreFactory $temp_store) {
    $this->tempStore = $temp_store;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore')
    );
  }

  /**
   * @return string
   *   Show the recent created document.
   */
  public function index() {

    $rethink_storage = $this->tempStore->get('rethinkdb');
    $params = [];

    if ($document_id = $rethink_storage->get('document_id')) {
      // Since we displaying only the created row we need to delete it once we
      // displayed it to the user.
      $rethink_storage->delete('document_id');
      $text = $this->t('You entered a new record to the DB - @record', ['@record' => $document_id]);
    }
    else {
      $params['@url'] = Url::fromRoute('rethinkdb_example.message_create_form')->toString();
      $text = $this->t('In order to show the last message you need to <a href="@url">create it</a>.', $params);
    }

    $text .= '<br />';
    $params['@url'] = Url::fromRoute('rethinkdb_example.message_list')->toString();
    $text .= $this->t('You can also watch the <a href="@url">other messages</a>.', $params);

    return [
      '#type' => 'markup',
      '#markup' => $text,
    ];
  }

}
