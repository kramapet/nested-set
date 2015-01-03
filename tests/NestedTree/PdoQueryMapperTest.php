<?php
/**
 * Tests for PDO query mapper
 */

namespace Tests\NestedTree;

use \NestedTree,
	NestedTree\Table,
	NestedTree\IQueryMapper,
	NestedTree\QueryMappers\PDOQueryMapper;

class PDOQueryMapperTest extends BaseTree_TestCase {

	/** @var IQueryMapper */
	protected $mapper;
	
	protected function setUp() {
		parent::setUp();

		$this->table = $this->createTable();
		$this->mapper = new PDOQueryMapper(self::getPdo(), $this->table->getTableName());
	}

	protected function tearDown() {
		parent::tearDown();

		$this->table = $this->mapper = NULL;
	}

	public function testIfMapperImplementsInterface() {
		$this->assertInstanceOf('NestedTree\IQueryMapper', $this->mapper);
	}

	public function testFetchAll() {
		$result = $this->mapper->fetchAll();
		$this->assertEquals(8, count($result->fetchAll()));
	}

	public function testFetchSinglePath() {
		$result = $this->mapper->fetchSinglePath(
			7, // id
			$this->table->getPrimaryKey(), // primary key
			$this->table->getLeft(), // left side
			$this->table->getRight() // right side
		);	

		$expected = array('alpha', 'gamma', 'eta');
		$actuals = array();
		foreach ($result as $row) {
			$actuals[] = $row['name'];
		}

		$this->assertEquals($expected, $actuals);
	}

	public function testFetchBetweenField() {
		$field = $this->table->getLeft();
		$result = $this->mapper->fetchBetweenField($field, 10, 13);	
		$expected_names = array('gamma', 'eta', 'theta');
		foreach ($result as $row) {
			$this->assertContains($row['name'], $expected_names);
		}
	}

	public function testFetchByField() {
		$row = $this->mapper->fetchByField($this->table->getPrimaryKey(), 1);	

		$this->assertEquals(1, $row[$this->table->getPrimaryKey()]);
		$this->assertEquals('alpha', $row['name']);
		$this->assertEquals(1, $row[$this->table->getLeft()]);
		$this->assertEquals(16, $row[$this->table->getRight()]);
	}

	public function testSetFieldGreaterThan() {
		$rows = $this->mapper->setFieldGreaterThan(
			$this->table->getLeft(), // field
			128, // value
			11 // greater than
		);

		$this->assertEquals(1, $rows);

		$table_name = $this->table->getTableName();
		$field_left = $this->table->getLeft();
		$pdo = self::getPdo();
		$row = $pdo->query("SELECT * 
			FROM $table_name 
			WHERE $field_left = 128")->fetch();

		$this->assertEquals('theta', $row['name']);

	}

	public function testAddNToFieldGreaterThan() {
		$table_name = $this->table->getTableName();
		$field = $this->table->getLeft();
		$n = 2;

		$affected = $this->mapper->addNToFieldGreaterThan($field, $n, 11);
		$this->assertEquals(1, $affected);

		$pdo = self::getPdo();

		// name=theta, (original) lft=13 - see datasets/treeDb.xml
		$row = $pdo->query("SELECT $field FROM $table_name WHERE id = 8")->fetch();
		$expected_lft = 15;

		$this->assertEquals($expected_lft, $row[$field]);
	}

	public function testSubtractNToFieldGreaterThan() {
		$table_name = $this->table->getTableName();
		$field = $this->table->getLeft();
		$n = 2;

		$affected = $this->mapper->subtractNToFieldGreaterThan($field, $n, 11);
		$this->assertEquals(1, $affected);

		$pdo = self::getPdo(); 
		
		// name=theta, (original) lft=13 - see datasets/treeDb.xml
		$row = $pdo->query("SELECT $field FROM $table_name WHERE id = 8")->fetch();
		$expected_lft = 11;

		$this->assertEquals($expected_lft, $row[$field]);
		
	}

	public function testDeleteBetweenField() {
		$table_name = $this->table->getTableName();

		$this->assertEquals(8, $this->mapper->deleteBetweenField($this->table->getLeft(), 1, 16)); // whole tree

		$pdo = self::getPdo();
		$row = $pdo->query("SELECT COUNT(*) as count FROM $table_name")->fetch();

		$this->assertEquals(0, $row['count']);
		
	}

	public function testInsert() {
		$field_left = $this->table->getLeft();
		$field_right = $this->table->getRight();
		$table_name = $this->table->getTableName();

		$data = array(
			'name' => 'foo',
			$field_left => 100,
			$field_right => 200
		);

		$this->assertEquals(1, $this->mapper->insert($data));

		$pdo = self::getPdo();
		$sql = "SELECT $field_left, $field_right
			FROM $table_name
			WHERE name = " . $pdo->quote($data['name']);

		$actual = $pdo->query($sql)->fetch();

		$this->assertEquals($data[$field_left], $actual[$field_left]);
		$this->assertEquals($data[$field_right], $actual[$field_right]);
	}

}
