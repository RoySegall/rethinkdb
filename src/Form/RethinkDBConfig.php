<?php

namespace Drupal\rethinkdb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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
      'rethinkdb',
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
    $config = $this->config('rethinkdb');

    $form['host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#description' => $this->t('RethinkDB host i.e http://localhost:28015'),
      '#default' => $config->get('host'),
    ];

    $form['db'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Table'),
      '#description' => $this->t('The name of the DB.'),
      '#default' => $config->get('db'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // todo validate connection before submitting.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('rethinkdb')
      ->set('host', $form_state->getValue('host'))
      ->set('db', $form_state->getValue('db'))
      ->save();
  }

}
