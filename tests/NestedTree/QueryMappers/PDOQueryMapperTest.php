<?php
/**
 * Tests for PDO query mapper
 */

namespace Tests\NestedTree\QueryMappers;

use \NestedTree,
	NestedTree\Table,
	NestedTree\IQueryMapper,
	NestedTree\QueryMappers\PDOQueryMapper;


class PDOQueryMapperTest extends AbstractQueryMapperTestCase {

	/** @var IQueryMapper */
	private $queryMapper;

	/** @var \PDO */
	private $pdo;

	protected function getQueryMapper() {
		if (!$this->queryMapper) {
			$table_name = $this->getTable()->getTableName();
			$pdo = $this->getPdo();
			$this->queryMapper = new PDOQueryMapper($pdo, $table_name);
		}

		return $this->queryMapper;
	}	

	protected function createTable() {
		$this->getPdo()->query($this->getCreateTableSql());
	}

	protected function fetch($id) {
		$id = $this->getPdo()->quote($id);
		$table_name = $this->getTable()->getTableName();
		$sql = "SELECT * FROM $table_name WHERE id = $id";
		return $this->getPdo()->query($sql)->fetch();
	}

	protected function numRows() {
		$table_name = $this->getTable()->getTableName();
		$sql = "SELECT COUNT(*) as count FROM $table_name";
		$row = $this->getPdo()->query($sql)->fetch();
		return (int) $row['count'];
	}

	protected function insertRow(array $data) {
		$table_name = $this->getTable()->getTableName();
		$values = $columns = array();

		foreach ($data as $field => $value) {
			$columns[] = $field;
			$values[] = $this->getPdo()->quote($value);
		}

		$columns = implode(',', $columns);
		$values = implode(',', $values);

		$sql = "INSERT INTO $table_name ($columns) VALUES ($values)";
		$this->getPdo()->query($sql);
	}

	private function getPdo() {
		if(!$this->pdo) {
			$this->pdo = new \PDO('sqlite::memory:');
		}

		return $this->pdo;
	}
}
