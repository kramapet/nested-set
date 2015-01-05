<?php
/**
 * Tests for Dibi Query Mapper
 */

namespace Tests\NestedSet\QueryMappers;

use \NestedSet,
	NestedSet\IQueryMapper,
	NestedSet\QueryMappers\DibiQueryMapper;

class DibiQueryMapperTest extends AbstractQueryMapperTestCase {

	/** @var IQueryMapper */
	private $queryMapper;

	/** @var \DibiConnection */
	private $dibi;
	
	protected function getQueryMapper() {
		if (!$this->queryMapper) {
			$dibi = $this->getDibi();
			$table_name = $this->getTable()->getTableName();
			$this->queryMapper = new DibiQueryMapper($dibi, $table_name);
		}

		return $this->queryMapper;
	}

	protected function createTable() {
		$this->getDibi()->query($this->getCreateTableSql());
	}

	protected function fetch($id) {
		$table_name = $this->getTable()->getTableName();
		$primary_key = $this->getTable()->getPrimaryKey();
		$sql = "SELECT * FROM %n WHERE %n = %i";
		$res = $this->getDibi()->query($sql, $table_name, $primary_key, $id);
		return $res->fetch();
	}

	protected function numRows() {
		$table_name = $this->getTable()->getTableName();
		$res = $this->getDibi()->query('SELECT COUNT(*) FROM %n', $table_name);
		return (int) $res->fetchSingle();
	}

	protected function insertRow(array $row) {
		$table_name = $this->getTable()->getTableName();
		$this->getDibi()->query('INSERT INTO %n', $table_name, $row);
	}

	private function getDibi() {
		if (!$this->dibi) {
			$conn = array(
				'driver' => 'sqlite3',
				'database' => ':memory:'
			);	

			$this->dibi = new \DibiConnection($conn);
		}

		return $this->dibi;
	}

}
