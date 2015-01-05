<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @licence MIT
 */

namespace NestedTree\QueryMappers;

use \NestedTree,
	NestedTree\ITable,
	NestedTree\IQueryMapper;

class PDOQueryMapper implements IQueryMapper {

	/** @var \PDO */
	private $db;

	/** @var string */
	private $table_name;

	public function __construct(\PDO $pdo, $table_name) {
		$this->db = $pdo;
		$this->table_name = $table_name;
	}

	public function fetchAll() {
		$sql = "SELECT * FROM {$this->table_name}";
		return $this->db->query($sql);
	}

	public function fetchBetweenField($field, $left, $right) {
		$left = $this->db->quote($left);
		$right = $this->db->quote($right);

		$sql = "SELECT * FROM {$this->table_name} WHERE $field BETWEEN $left AND $right";
		return $this->db->query($sql);
	}

	public function fetchByField($field, $value) {
		$value = $this->db->quote($value);

		$sql = "SELECT * FROM {$this->table_name} WHERE $field = $value";
		return $this->db->query($sql)->fetch();
	}

	public function fetchSinglePath($id, $pk_field,  $left_field, $right_field) {
		$id = $this->db->quote($id);
		$sql = "SELECT parent.* 
				FROM {$this->table_name} as parent, 
					 {$this->table_name} as node
				WHERE node.{$left_field} 
				BETWEEN parent.{$left_field} 
				AND parent.{$right_field}
				AND node.{$pk_field} = $id";
		return $this->db->query($sql);
	}

	public function addNToFieldGreaterThan($field, $n, $greaterThan) {
		$n = $this->db->quote($n);
		$greaterThan = $this->db->quote($greaterThan);

		$sql = "UPDATE {$this->table_name} SET $field = $field + $n WHERE $field > $greaterThan";
		$stmt = $this->db->query($sql);
		return $stmt->rowCount();
	}

	public function subtractNToFieldGreaterThan($field, $n, $greaterThan) {
		$n = $this->db->quote($n);
		$greaterThan = $this->db->quote($greaterThan);

		$sql = "UPDATE {$this->table_name} SET $field = $field - $n WHERE $field > $greaterThan";	
		$stmt = $this->db->query($sql);
		return $stmt->rowCount();
	}

	public function deleteBetweenField($field, $left, $right) {
		$left = $this->db->quote($left);
		$right = $this->db->quote($right);

		$sql = "DELETE FROM {$this->table_name} 
				WHERE $field BETWEEN $left AND $right";	
		$stmt = $this->db->query($sql);
		return $stmt->rowCount();
	}

	public function insert(array $fields) {
		$sql = $this->buildInsertQuery($this->table_name, $fields);		
		$stmt = $this->db->query($sql);
		return $this->db->lastInsertId();
	}

	private function buildInsertQuery($tablename, array $fields) {
		$columns = $values = array();

		foreach ($fields as $field => $value) {
			$columns[] = $field;
			$values[] = $this->db->quote($value);
		}

		$columns = implode(',', $columns);
		$values = implode(',', $values);

		return "INSERT INTO $tablename ($columns) VALUES ($values)";
	}
}
