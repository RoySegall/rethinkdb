<?php

namespace Drupal\rethinkdb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use r\Exceptions\RqlDriverError;
use r\Queries\Dbs\Db;

/**
 * Class RethinkDBConfig.
 *
 * @package Drupal\rethinkdb\Form
 */
class RethinkDBConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rethinkdb.database',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rethinkdb_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rethinkdb.database');

    $form['host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#description' => $this->t('RethinkDB host i.e localhost'),
      '#default_value' => $config->get('host'),
    ];

    $form['port'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#description' => $this->t('The port of the DB. Default to 28015'),
      '#default_value' => $config->get('port'),
    ];

    $form['database'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Table'),
      '#description' => $this->t('The name of the DB.'),
      '#default_value' => $config->get('database'),
    ];

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#description' => $this->t('The API key of the DB connection.'),
      '#default_value' => $config->get('api_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    try {
      \r\connect($form_state->getValue('host'), $form_state->getValue('port'), $form_state->getValue('database'), $form_state->getValue('apiKey'), $form_state->getValue('timeout'));
    } catch (\Exception $e) {
      $form_state->setErrorByName('host', $e->getMessage());
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('rethinkdb.database')
      ->set('host', $form_state->getValue('host'))
      ->set('port', $form_state->getValue('port'))
      ->set('db', $form_state->getValue('db'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();
  }

}
