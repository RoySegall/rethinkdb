<?php

/**
 * @contains \Drupal\rethinkdb\RethinkDB.
 */

namespace Drupal\rethinkdb;

use Drupal\Core\Site\Settings;
use r\Connection;
use r\Queries\Tables\Table;

class RethinkDB {

  /**
   * @var Connection
   *
   * The connection object.
   */
  protected $connection;

  public function __construct(Settings $settings) {
    $this->setConnection(\r\connect());
  }

  /**
   * Get the connection for the DB.
   *
   * @return Connection
   */
  public function getConnection() {
    return $this->connection;
  }

  /**
   * Set the connection object.
   *
   * @param Connection $connection
   *   A connection object.
   *
   * @return RethinkDB
   */
  public function setConnection(Connection $connection) {
    $this->connection = $connection;
    return $this;
  }

  /**
   * @param $table
   *
   * @return Table
   */
  public function getTable($table) {
    return \r\table($table);
  }

  /**
   * @param $table
   */
  public function tableCreate($table) {
    \r\db('test')->tableCreate($table)->run($this->getConnection());
  }

  /**
   * @param $table
   * @param $values
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function insert($table, $values) {
    return $this->getTable($table)->insert($values)->run($this->getConnection());
  }

}
