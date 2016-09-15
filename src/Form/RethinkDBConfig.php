<?php

namespace Drupal\rethinkdb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rethinkdb\RethinkDB;

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

    $submit_disabled = FALSE;
    if (!RethinkDB::getService()->validateConnection($config->get('host'), $config->get('port'))) {
      $submit_disabled = TRUE;
      drupal_set_message(t('Please check your RethinkDB DB is up and running. There is problem connecting to the DB'), 'error');
    }
    else {
      if (!in_array($config->get('database'), RethinkDB::getService()->dbList())) {
        $submit_disabled = TRUE;
        drupal_set_message(t('The DB %name does not exists.', ['%name' => $config->get('database')]), 'error');
      }
    }

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

    $form['timeout'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Timeout'),
      '#description' => $this->t('Define the amount ot timeout in the connection'),
      '#default_value' => $config->get('timeout'),
    ];

    $form = parent::buildForm($form, $form_state);

    $form['actions']['submit']['#disabled'] = $submit_disabled;

    return $form;
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
      ->set('database', $form_state->getValue('database'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('timeout', $form_state->getValue('timeout'))
      ->save();
  }

}
