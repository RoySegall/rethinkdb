<?php

/**
 * @contains \Drupal\rethinkdb\RethinkDB.
 */

namespace Drupal\rethinkdb;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use r\Connection;
use r\Exceptions\RqlDriverError;
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
   * An alias to the RethinkDB service.
   *
   * @return RethinkDB
   */
  public static function getService() {
    return \Drupal::service('rethinkdb');
  }

  /**
   * RethinkDB constructor.
   *
   * @param ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {

    $config = $config_factory->get('rethinkdb.database');

    $info = $config->getRawData();

    if ($this->validateConnection($info['host'], $info['port'])) {
      try {
        $this->setConnection(\r\connect($info['host'], $info['port'], $info['database'], $info['api_key'], $info['timeout']));
      } catch (RqlDriverError $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
    }

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
   * Validate the RethinkDB connection.
   *
   * @param $host
   *   The address of the RethinkDB.
   * @param $port
   *   The port which we establish the connection.
   *
   * @return bool
   */
  public function validateConnection($host, $port) {
    return @fsockopen($host, $port) !== FALSE;
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
   * @param $delete_if_exists
   *   Optional. Delete the DB if exists. Default to TRUE.
   *
   * @throws \Exception
   *
   * @return RethinkDB
   */
  public function createDb($db = NULL, $delete_if_exists = TRUE) {
    if (!$db) {
      $db = $this->settings['database'];
    }

    $list = $this->dbList();

    if (in_array($this->settings['database'], $list)) {
      if ($delete_if_exists) {
        \r\dbDrop($db)->run($this->getConnection());
      }
      else {
        throw new \Exception("A database with the name {$db} already exists.");
      }
    }

    \r\dbCreate($db)->run($this->getConnection());

    return $this;
  }

  /**
   * Get a list of available DBs.
   *
   * @return array
   *   List of all the DB installed on RethinkDB.
   */
  public function dbList() {
    return \r\dbList()->run($this->getConnection());
  }

  /**
   * Get the table object query-ready to use.
   *
   * @param $table
   *   The table name.
   *
   * @return Table
   */
  public function getTable($table) {
    return \r\table($table);
  }

  /**
   * Creating a table.
   *
   * @param $table
   *   The table name.
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function tableCreate($table) {
    return $this->getDb()->tableCreate($table)
      ->run($this->getConnection());
  }

  /**
   * Remove the table from the DB.
   *
   * @param $table
   *   The table name.
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function tableDrop($table) {
    return $this->getDb()->tableDrop($table)
      ->run($this->getConnection());
  }

  /**
   * Creating an index in a table.
   *
   * @param $table
   *   The table name.
   * @param $key
   *   The key for the new index.
   *
   * @return \r\Queries\Index\IndexCreate
   */
  public function createIndex($table, $key) {
    return $this->getDb()->table($table)->indexCreate($key)->run($this->getConnection());
  }

  /**
   * Insert a document into the DB.
   *
   * @param $table
   *   The table name.
   * @param $document
   *   The document object.
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function insert($table, $document) {
    return $this->getTable($table)->insert($document)->run($this->getConnection());
  }

  /**
   * Load multiple items from the DB.
   *
   * @param $table_name
   *   The table name.
   * @param array $ids
   *   Array of documents IDs.
   *
   * @return \r\Queries\Selecting\GetAll
   */
  public function getAll($table_name, array $ids) {
    return $this->getAllWrapper($table_name, $ids)->run($this->getConnection())->toArray();
  }

  /**
   * Deleting multiple documents from table.
   *
   * @param $table_name
   *   The table name.
   * @param array $ids
   *   List of IDs.
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function deleteAll($table_name, array $ids) {
    return $this->getAllWrapper($table_name, $ids)->delete()->run($this->getConnection());
  }

  /**
   * Wrapping the get all logic.
   *
   * @param $table_name
   *   The table name.
   * @param array $ids
   *   List of IDs.
   *
   * @return \r\Queries\Selecting\GetAll
   */
  protected function getAllWrapper($table_name, array $ids) {
    return $this->getTable($table_name)->getAll(\r\args($ids));
  }

  /**
   * Update a document in the DB.
   *
   * @param $table
   *   The table name.
   * @param $document
   *   The document object.
   *
   * @return array|\ArrayObject|\DateTime|null|\r\Cursor
   */
  public function update($table, $document) {
    return $this->getTable($table)->update($document)->run($this->getConnection());
  }

}
