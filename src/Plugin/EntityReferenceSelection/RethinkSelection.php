<?php

/**
 * @file
 * Contains \Drupal\rethinkdb\Plugin\EntityReferenceSelection\RethinkSelection.
 */

namespace Drupal\rethinkdb\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * @EntityReferenceSelection(
 *   id = "default:rethinkdb_default",
 *   label = @Translation("RethinkDB selection"),
 *   group = "default",
 *   weight = 1
 * )
 */
class RethinkSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    return \Drupal::entityQuery($this->configuration['target_type']);
  }

}
