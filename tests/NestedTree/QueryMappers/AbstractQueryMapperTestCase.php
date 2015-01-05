<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @licence MIT
 * @description TestCase class for testing QueryMappers
 */


namespace Tests\NestedTree\QueryMappers;

use \NestedTree\ITable,
	\NestedTree\Table;

abstract class AbstractQueryMapperTestCase extends \PHPUnit_Framework_TestCase {

	/** @var ITable */
	protected $table;

	abstract protected function getQueryMapper();
	abstract protected function createTable();
	abstract protected function fetch($id);
	abstract protected function numRows();
	abstract protected function insertRow(array $row);

	protected function setUp() {
		parent::setUp();
		$this->createTable();
		$this->insertDefaultDataSet();
	}

	protected function getTable() {
		if (!$this->table) {
			$this->table = new Table('tree', 'id', 'lft', 'rgt');
		}

		return $this->table;
	}

	public function testIfMapperImplementsIQueryMapper() {
		$this->assertInstanceOf('NestedTree\IQueryMapper', $this->getQueryMapper());
	}	

	public function testFetchAll() {
		$result = $this->getQueryMapper()->fetchAll();

		$count = 0;
		foreach ($result as $r) {
			$count++;
		}

		$this->assertEquals(count($this->getDefaultDataSet()), $count);
	}

	public function testFetchSinglePath() {
		$mapper = $this->getQueryMapper();
		$nid = 7;
		$pk = $this->getTable()->getPrimaryKey();
		$left = $this->getTable()->getLeft();
		$right = $this->getTable()->getRight();

		$actuals = array();
		foreach ($mapper->fetchSinglePath($nid, $pk, $left, $right) as $row) {
			$actuals[] = $row['name'];
		}

		$expected = array('alpha', 'gamma', 'eta');
		$this->assertEquals($expected, $actuals);
	}

	public function testFetchBetweenField() {
		$field = $this->getTable()->getLeft();
		$result = $this->getQueryMapper()->fetchBetweenField($field, 10, 13);
		$expected_names = array('gamma', 'eta', 'theta');
		foreach ($result as $row) {
			$this->assertContains($row['name'], $expected_names);
		}
	}

	public function testFetchByField() {
		$pk = $this->getTable()->getPrimaryKey();
		$row = $this->getQueryMapper()->fetchByField($pk, 1);

		$this->assertEquals(1, $row[$pk]);
		$this->assertEquals('alpha', $row['name']);
		$this->assertEquals(1, $row[$this->getTable()->getLeft()]);
		$this->assertEquals(16, $row[$this->getTable()->getRight()]);
	}

	public function testAddNToFieldGreaterThan() {
		$field = $this->getTable()->getLeft();
		$n = 2;

		$affected = $this->getQueryMapper()->addNToFieldGreaterThan($field, $n, 11);
		$this->assertEquals(1, $affected);

		// name=theta, (original) lft=13 - see datasets/treeDb.xml
		$row = $this->fetch(8);
		$expected_lft = 15;

		$this->assertEquals($expected_lft, $row[$field]);
	}

	public function testSubtractNToFieldGreaterThan() {
		$field = $this->getTable()->getLeft();
		$n = 2;

		$affected = $this->getQueryMapper()->subtractNToFieldGreaterThan($field, $n, 11);
		$this->assertEquals(1, $affected);

		// name=theta, (original) lft=13 - see datasets/treeDb.xml
		$row = $this->fetch(8);
		$expected_lft = 11;

		$this->assertEquals($expected_lft, $row[$field]);
		
	}

	public function testDeleteBetweenField() {
		$left = $this->getTable()->getLeft();

		// delete whole tree
		$this->assertEquals(8, $this->getQueryMapper()->deleteBetweenField($left, 1, 16));
		$this->assertEquals(0, $this->numRows());
		
	}

	public function testInsert() {
		$primary_key = $this->getTable()->getPrimaryKey();
		$field_left = $this->getTable()->getLeft();
		$field_right = $this->getTable()->getRight();

		$data = array(
			'name' => 'foo',
			$field_left => 100,
			$field_right => 200
		);

		$data[$primary_key] = $this->getQueryMapper()->insert($data);
		$this->assertNotNull($data[$primary_key]);

		$actual = $this->fetch($data[$primary_key]);

		$this->assertEquals($data[$field_left], $actual[$field_left]);
		$this->assertEquals($data[$field_right], $actual[$field_right]);
	}

	protected function insertDefaultDataSet() {
		foreach ($this->getDefaultDataSet() as $set) {
			$this->insertRow(array(
				$this->getTable()->getPrimaryKey() => $set[0],
				'name' => $set[1],
				$this->getTable()->getLeft() => $set[2],
				$this->getTable()->getRight() => $set[3]
			));
		}
	}

	protected function getDefaultDataSet() {
		return array(
			array(1, 'alpha', 1, 16),
			array(2, 'beta', 2, 9),
			array(3, 'gamma', 10, 15),
			array(4, 'delta', 3, 4),
			array(5, 'epsilon', 5, 6),
			array(6, 'zeta', 7, 8),
			array(7, 'eta', 11, 12),
			array(8, 'theta', 13, 14)
		);
	}

	protected function getCreateTableSql() {
		$t = $this->getTable();
		$table_name = $t->getTableName();
		$primary_key = $t->getPrimaryKey();
		$left = $t->getLeft();
		$right = $t->getRight();

		return "CREATE TABLE $table_name (
			$primary_key INTEGER PRIMARY KEY,
			$left INTEGER NOT NULL,
			$right INTEGER NOT NULL,
			name TEXT NOT NULL
		);";

	}
}
