<?php
/**
 * test for 'NestedTree\Tree'
 */

namespace Tests\NestedTree;

abstract class BaseTree_TestCase extends \PHPUnit_Extensions_Database_TestCase {
	/** @var \PDO */
	private static $pdo;

	/** @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection */
	private $conn;

	/**
	 * Get sqlite PDO object
	 * @return \PDO
	 */
	static public function getPdo() {
		if (!self::$pdo) {
			self::$pdo = new \PDO('sqlite:' . __DIR__ . '/datasets/db.sqlite');
		}

		return self::$pdo;
	}

	/**
	 * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	final public function getConnection() {

		if (!$this->conn) {
			$this->conn = $this->createDefaultDBConnection(self::getPdo(), 'db.sqlite');	
		}

		return $this->conn;
	}

	/**
	 * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return $this->createFlatXmlDataSet(__DIR__ . '/datasets/treeDb.xml');
	}
}
