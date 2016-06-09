<?php

/**
 * @contains \Drupal\rethinkdb\RethinkDB.
 */

namespace Drupal\rethinkdb;

use Drupal\Core\Site\Settings;
use r\Connection;
use r\Queries\Dbs\Db;
use r\Queries\Tables\Table;

class RethinkDB {

  /**
   * @var Connection
   *
   * The connection object.
   */
  protected $connection;

  /**
   * @var array
   *
   * Array with the information of the connection.
   */
  protected $settings;

  /**
   * RethinkDB constructor.
   *
   * @param Settings $settings
   *   The global object settings. Define the database connection in the
   *   settings.php file.
   */
  public function __construct(Settings $settings) {
    $info = $settings->get('rethinkdb') + [
      'host' => 'localhsot',
      'port' => '28015',
      'apiKey' => NULL,
      'timeout' => NULL,
    ];
    $this->setConnection(\r\connect($info['host'], $info['port'], $info['database'], $info['apiKey'], $info['timeout']));
    $this->setSettings($info);
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
   * Get the settings of the DB connection.
   *
   * @return array
   */
  public function getSettings() {
    return $this->settings;
  }

  /**
   * Setter for settings property.
   *
   * @param $settings
   *  The settings relate to the DB connection.
   *
   * @return RethinkDB
   */
  public function setSettings($settings) {
    $this->settings = $settings;
    return $this;
  }

  /**
   * Get the DB object.
   *
   * @return Db
   */
  public function getDb() {
    return \r\db($this->settings['database']);
  }

  /**
   * Create a DB in the server.
   *
   * @param $db
   *   Optional. The database name. If not provided the database from the
   *   rethinkdb settings will used.
   *
   * @throws \Exception
   */
  public function createDb($db = NULL) {
    if (!$db) {
      $db = $this->settings['database'];
    }

    $list = \r\dbList()->run($this->getConnection());

    if (in_array($this->settings['database'], $list)) {
      throw new \Exception("A database with the name {$db} already exists.");
    }

    \r\dbCreate($db)->run($this->getConnection());
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
   *
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function tableCreate($table) {
    return $this->getDb()->tableCreate($table)
      ->run($this->getConnection());
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
