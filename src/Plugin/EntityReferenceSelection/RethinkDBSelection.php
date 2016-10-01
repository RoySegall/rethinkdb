<?php

namespace Drupal\rethinkdb\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'selection' entity_reference for RethinkDB.
 *
 * @EntityReferenceSelection(
 *   id = "rethinkdb",
 *   label = @Translation("RethinkDB entities selection based"),
 *   group = "rethinkdb"
 * )
 */
class RethinkDBSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $key = empty($this->configuration['handler_settings']['search_key']) ? '' : $this->configuration['handler_settings']['search_key'];
    $form['search_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search field'),
      '#description' => $this->t('The key on which the query will match the text to input of the user.'),
      '#default_value' => $key,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $query = $this->buildEntityQuery($match, $match_operator);
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $entity_ids = $query->execute();

    if (empty($entity_ids)) {
      return array();
    }

    $entities = \Drupal::entityTypeManager()
      ->getStorage($this->configuration['target_type'])
      ->loadMultiple($entity_ids);

    $handler_settings = $this->configuration['handler_settings'];
    $options = array();

    foreach ($entities as $entity) {
      $value = $entity->getValues();
      $options[$this->configuration['target_type']][$value['id']] = Html::escape($value[$handler_settings['search_key']]);
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    return $this->buildEntityQuery($match, $match_operator)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function validateReferenceableEntities(array $ids) {
    $result = array();

    if ($ids) {
      return $this->buildEntityQuery()
        ->condition('id', reset($ids))
        ->execute();
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function validateReferenceableNewEntities(array $entities) {
    return array_filter($entities, function ($entity) {
      if (isset($this->configuration['handler_settings']['target_bundles'])) {
        return in_array($entity->bundle(), $this->configuration['handler_settings']['target_bundles']);
      }
      return TRUE;
    });
  }

  /**
   * Builds an EntityQuery to get referenceable entities.
   *
   * @param string|null $match
   *   (Optional) Text to match the label against. Defaults to NULL.
   * @param string $match_operator
   *   (Optional) The operation the matching should be done with. Defaults
   *   to "CONTAINS".
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The EntityQuery object with the basic conditions and sorting applied to
   *   it.
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $handler_settings = $this->configuration['handler_settings'];

    $query = $this->entityManager->getStorage($this->configuration['target_type'])->getQuery();

    if (isset($match)) {
      if ($match_operator == '=' && preg_match("/.+\s\((\S+)\)/", $match, $matches)) {
        $query->condition('id', $matches[1], $match_operator);
      }
      else {
        $query->condition($handler_settings['search_key'], $match, $match_operator);
      }
    }

    // Add the sort option.
    if (!empty($handler_settings['sort'])) {
      $sort_settings = $handler_settings['sort'];
      if ($sort_settings['field'] != '_none') {
        $query->sort($sort_settings['field'], $sort_settings['direction']);
      }
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function entityQueryAlter(SelectInterface $query) { }

}
