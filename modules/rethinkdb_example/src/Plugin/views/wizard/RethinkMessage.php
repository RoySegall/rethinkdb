<?php

/**
 * @file
 * Definition of Drupal\node\Plugin\views\wizard\Node.
 */

namespace Drupal\rethinkdb_example\Plugin\views\wizard;

use Drupal\views\Plugin\views\wizard\WizardPluginBase;

/**
 * Adding support for rethinkdb example with views.
 *
 * @ViewsWizard(
 *   id = "rethinkdb_message",
 *   base_table = "rethinkdb_message",
 *   title = @Translation("RethinkDB Example")
 * )
 */
class RethinkMessage extends WizardPluginBase {

  /**
   * Set default values for the path field options.
   */
  protected $pathField = [
    'id' => 'id',
    'table' => 'rethinkdb_example',
    'field' => 'id',
    'exclude' => TRUE,
    'link_to_user' => FALSE,
  ];

  /**
   * Set default values for the filters.
   */
  protected $filters = [
    'status' => [
      'value' => TRUE,
      'table' => 'message',
      'field' => 'status',
      'provider' => 'message',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  protected function defaultDisplayOptions() {
    $display_options = parent::defaultDisplayOptions();

    // Add permission-based access control.
    $display_options['access']['type'] = 'perm';
    $display_options['access']['provider'] = 'user';

    unset($display_options['fields']);

    // Remove the default fields, since we are customizing them here.
    /* Field: Message: Text */
    $display_options['fields']['name']['id'] = 'mid';
    $display_options['fields']['name']['table'] = 'message';
    $display_options['fields']['name']['field'] = 'text';
    $display_options['fields']['name']['provider'] = 'message';
    $display_options['fields']['name']['label'] = t('Message text');
    $display_options['fields']['name']['alter']['alter_text'] = 0;
    $display_options['fields']['name']['alter']['make_link'] = 0;
    $display_options['fields']['name']['alter']['absolute'] = 0;
    $display_options['fields']['name']['alter']['trim'] = 0;
    $display_options['fields']['name']['alter']['word_boundary'] = 0;
    $display_options['fields']['name']['alter']['ellipsis'] = 0;
    $display_options['fields']['name']['alter']['strip_tags'] = 0;
    $display_options['fields']['name']['alter']['html'] = 0;
    $display_options['fields']['name']['hide_empty'] = 0;
    $display_options['fields']['name']['empty_zero'] = 0;
    $display_options['fields']['name']['link_to_taxonomy'] = 1;

    return $display_options;
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultDisplayFilters($form, $form_state) {
    $filters = [];

    return $filters;
  }
}
