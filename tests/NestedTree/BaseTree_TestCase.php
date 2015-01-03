<?php
/**
 * test for 'NestedTree\Tree'
 */

namespace Tests\NestedTree;

use \NestedTree,
	NestedTree\Table,
	NestedTree\Tree,
	NestedTree\QueryMappers\PDOQueryMapper;

abstract class BaseTree_TestCase extends \PHPUnit_Extensions_Database_TestCase {
	const DATABASE = ':memory:';

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
			self::$pdo = new \PDO('sqlite:' . self::DATABASE);
			self::createSchema(self::$pdo, __DIR__ . '/datasets/schema.sqlite');
		}

		return self::$pdo;
	}

	/**
	 * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	final public function getConnection() {

		if (!$this->conn) {
			$pdo = self::getPdo();
			$this->conn = $this->createDefaultDBConnection(
				self::getPdo(), 
				self::DATABASE
			);	

		}


		return $this->conn;
	}

	protected function createTree() {
		$table = $this->createTable();
		$mapper = new PDOQueryMapper(self::getPdo(), $table->getTableName());
		return new Tree($mapper, $table);
	}

	protected function createTable() {
		return new Table('tree', 'id', 'lft', 'rgt');
	}

	/**
	 * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return $this->createFlatXmlDataSet(__DIR__ . '/datasets/treeDb.xml');
	}

	private static function createSchema(\PDO $pdo, $fn) {
		$sql = file_get_contents($fn);

		try {
			$pdo->query($sql);
		} catch (\PDOException $pe) {
			throw $pe;
		}
	}
}
