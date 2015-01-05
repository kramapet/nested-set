<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @licence MIT
 */

namespace NestedSet\QueryMappers;

use \NestedSet,
	NestedSet\IQueryMapper;

class DibiQueryMapper implements IQueryMapper {

	/** @var \DibiConnection */
	protected $dibi;
	/** @var string */
	protected $table_name;

	function __construct(\DibiConnection $dibi, $table_name) {
		$this->dibi = $dibi;
		$this->table_name = $table_name;
	}
	
	public function fetchAll() {
		return $this->dibi->query('SELECT * FROM %n', $this->table_name);
	}

	public function fetchBetweenField($field, $left, $right) {
		$sql = 'SELECT * FROM %n WHERE %n BETWEEN %i AND %i';
		return $this->dibi->query($sql, $this->table_name, $field, $left, $right);
	}

	public function fetchByField($field, $value) {
		$sql = 'SELECT * FROM %n WHERE %n = %s';
		return $this->dibi->query($sql, $this->table_name, $field, $value)->fetch();
	}

	public function fetchSinglePath($id, $pk, $left, $right) {
		$sql = 'SELECT [parent].*
				FROM %n AS [parent], %n AS [node]
				WHERE [node].%n 
				BETWEEN [parent].%n AND [parent].%n
				AND [node].%n = %s';	
		return $this->dibi->query($sql, 
			$this->table_name, $this->table_name, 
			$left, 
			$left, $right, 
			$pk, $id
		);

	}

	public function addNToFieldGreaterThan($field, $n, $greaterThan) {
		$sql = 'UPDATE %n SET %n = %n + %i WHERE %n > %i';
		$this->dibi->query($sql, $this->table_name, $field, $field, $n, $field, $greaterThan);
		return $this->dibi->getAffectedRows();
	}

	public function subtractNToFieldGreaterThan($field, $n, $greaterThan) {
		$sql = 'UPDATE %n SET %n = %n - %i WHERE %n > %i';
		$this->dibi->query($sql, $this->table_name, $field, $field, $n, $field, $greaterThan);
		return $this->dibi->getAffectedRows();
	}

	public function deleteBetweenField($field, $left, $right) {
		$sql = 'DELETE FROM %n WHERE %n BETWEEN %i AND %i';
		$this->dibi->query($sql, $this->table_name, $field, $left, $right);
		return $this->dibi->getAffectedRows();
	}

	public function insert(array $data) {
		$this->dibi->query('INSERT INTO %n', $this->table_name, $data);
		return $this->dibi->getInsertId();
	}

}
