<?php

/**
 * @file
 * Contains Drupal\rethinkdb_websocket\Form\RethinkDBWebsocket.
 */

namespace Drupal\rethinkdb_websocket\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;

/**
 * Class RethinkDBWebsocket.
 *
 * @package Drupal\rethinkdb_websocket\Form
 */
class RethinkDBWebsocket extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['rethinkdb_websocket.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rethinkdb_webscoket';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rethinkdb_websocket.settings');
    $form['app_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Pusher app ID'),
      '#description' => $this->t('The pusher app ID'),
      '#default_value' => $config->get('pusher_app_id'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('rethinkdb_websocket.settings')
      ->set('pusher_app_id', $form_state->getValue('app_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
