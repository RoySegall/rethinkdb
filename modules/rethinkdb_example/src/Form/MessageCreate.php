<?php

/**
 * @file
 * Contains Drupal\rethinkdb_example\Controller\MessageCreate.
 */

namespace Drupal\rethinkdb_example\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MessageCreate.
 *
 * @package Drupal\rethinkdb_example\Controller
 */
class MessageCreate extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * @var PrivateTempStoreFactory
   */
  protected $tempStore;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_manager, PrivateTempStoreFactory $temp_store) {
    $this->entityManager = $entity_manager;
    $this->tempStore = $temp_store;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('user.private_tempstore')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'rethinkdb_message_create';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return [
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Message title'),
        '#required' => TRUE,
      ],
      'body' => [
        '#type' => 'textarea',
        '#title' => $this->t('Message body'),
        '#required' => TRUE,
      ],
      'actions' => [
        '#type' => 'action',
        'submit' => [
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
        ],
      ],
    ];
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $results = $this
      ->entityManager
      ->getStorage('rethinkdb_message')
      ->create([
        'title' => $form_state->getValue('title'),
        'body' => $form_state->getValue('body'),
      ])
      ->save();

    $this->tempStore->get('rethinkdb')->set('document_id', implode($results['generated_keys']));

    drupal_set_message($this->t('The message created successfully.'));
    $form_state->setRedirect('rethinkdb_example.creation_result');
  }

}
