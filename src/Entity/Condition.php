<?php

/**
 * @file
 * Contains Drupal\rethinkdb\Entity\Condition.
 */

namespace Drupal\rethinkdb\Entity;

use Drupal\Core\Entity\Query\ConditionBase;
use Drupal\Core\Entity\Query\ConditionInterface;
use Drupal\Core\Entity\Query\QueryException;

class Condition extends ConditionBase {

  /**
   * Queries for the existence of a field.
   *
   * @param $field
   * @param string $langcode
   * @return ConditionInterface
   * @see \Drupal\Core\Entity\Query\QueryInterface::exists()
   */
  public function exists($field, $langcode = NULL) {
    $this->condition($field, TRUE, '$exists', $langcode);
    return $this;
  }

  /**
   * Queries for the existence of a field.
   *
   * @param string $field
   * @return ConditionInterface;
   * @see \Drupal\Core\Entity\Query\QueryInterface::notexists()
   */
  public function notExists($field, $langcode = NULL) {
    $this->condition($field, FALSE, '$exists', $langcode);
    return $this;
  }

  /**
   * Compiles this conditional clause.
   *
   * @param $query
   *   The query object this conditional clause belongs to.
   */
  public function compile($entityType) {
    /** @var \Drupal\Core\Entity\EntityType $entityType */
    $entity_type_id = $this->query->getEntityTypeId();
    $entity_manager = \Drupal::entityManager();
    $langcode_key = \Drupal::entityManager()->getDefinition($entity_type_id)->getKey('langcode');
    $and =  strtolower($this->conjunction) == 'and';
    foreach ($this->conditions as $key => $condition) {
      if (isset($condition['langcode'])) {
        /** @var \Drupal\mongodb\Entity\Condition $group */
        $group = new static('and', $this->query);
        $group->condition($condition['field'], $condition['value'], $condition['operator']);
        $group->condition($langcode_key, $condition['langcode']);
        unset($this->conditions[$key]);
        $this->condition($group);
      }
    }

    $field_storage_definitions = $entity_manager->getFieldStorageDefinitions($entity_type_id);
    $find = array();
    $query_index = 0;
    foreach ($this->conditions as $condition) {
      $query_field = $condition['field'];
      if ($query_field instanceOf ConditionInterface) {
        if ($compiled = $query_field->compile($entityType)) {
          if ($and) {
            $find['$and'][++$query_index] = $compiled;
          }
          else {
            $find['$or'][] = $compiled;
          }
        }
      }
      else {
        if (!isset($condition['operator'])) {
          $condition['operator'] = is_array($condition['value']) ? '$in' : '=';
        }
        list($field, $column) = explode('.', $query_field, 2) + array(1 => '');
        if (!isset($field_storage_definitions[$field])) {
          throw new QueryException(String::format('@column not found', array('@column' => $column)));
        }
        $columns = $field_storage_definitions[$field]->getSchema()['columns'];
        if (!$column) {
          $column = count($columns) == 1 ? array_keys($columns)[0] : 'value';
        }
        $translated_condition = $this->translateCondition($condition, $columns[$column]['type']);
        if ($and) {
          if (isset($find['$and'][$query_index]['values']['$elemMatch'][$field]['$elemMatch'][$column])) {
            $this->merge($find['$and'][$query_index]['values']['$elemMatch'][$field]['$elemMatch'][$column], $translated_condition);
          }
          else {
            // The first $elemMatch is required to keep unspecified langcode
            // conditions within the same translation, the second is to keep
            // the same fields within the same delta.
            $find['$and'][$query_index]['values']['$elemMatch'][$field]['$elemMatch'][$column] = $translated_condition;
          }
        }
        else {
          $find['$or'][] = array("values.$field.$column" => $translated_condition);
        }
      }
    }
    if ($and) {
      if ($query_index) {
        $find['$and'] = array_values($find['$and']);
      }
      elseif ($find) {
        $find = $find['$and'][0];
      }
    }
    return $find;
  }

  protected function translateCondition(array $condition, $type) {
    $value = MongoCollectionFactory::castValue($type, $condition['value']);
    $operator = $condition['operator'];
    if ($operator[0] === '$') {
      return array($operator => $value);
    }
    switch ($operator) {
      case '='           : return array('$eq' => $value);
      case 'IN'          : return array('$in' => $value);
      case 'NOT IN'      : return array('$nin' => $value);
      case '<'           : return array('$lt' => $value);
      case '>'           : return array('$gt' => $value);
      case '<='          : return array('$lte' => $value);
      case '>='          : return array('$gte' => $value);
      case '!='          :
      case '<>'          : return array('$ne' => $value);
      case 'STARTS_WITH' : return new \MongoRegex('/^' . preg_quote($value, '/') . '/');
      case 'CONTAINS'    : return new \MongoRegex('/' . preg_quote($value, '/') . '/i');
      case 'ENDS_WITH'   : return new \MongoRegex('/' . preg_quote($value, '/') . '/$i');
      case 'BETWEEN'     : return array('$gte' => $value[0], '$lte' => $value[1]);
      default :
        throw new QueryException(String::format('@operator not implemented', array('@operator' => $operator)));
    }
  }

  protected function merge(&$old_array, $new_value) {
    // @TODO this doesnt work with regexps.
    list($op, $value) = each($new_value);
    if (isset($old_array[$op])) {
      $old_value = $old_array[$op];
      switch ($op) {
        case '$eq':
          if ($value !== $new_value) {
            // Impossible condition.
            $old_array = ['$gt' > new \MongoMaxKey()];
          }
          break;
        case '$in': case '$nin':
          $old_array[$op] = array_unique(array_merge($old_value, $value));
          break;
      }
    }
    else {
      $old_array[$op] = $value;
    }
  }

}
