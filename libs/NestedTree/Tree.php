<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT
 */

namespace NestedTree;


class Tree implements ITree {
	
	/** @var IQueryMapper */
	private $mapper;

	/** @var ITable */
	private $table;

	public function __construct(IQueryMapper $mapper, ITable $table) {
		$this->mapper = $mapper;
		$this->table = $table;
	}

	public function getTree() {
		return $this->mapper->fetchAll();
	}

	public function getSubtree($id) {
		$primary_key = $this->table->getPrimaryKey();
		$row = $this->mapper->fetchByField($primary_key, $id);

		return $this->mapper->fetchBetweenField(
			$this->table->getLeft(), 
			$row[$this->table->getLeft()],
			$row[$this->table->getRight()]
		);
	}

	public function getSinglePath($id) {
		$pk = $this->table->getPrimaryKey();
		$left = $this->table->getLeft();
		$right = $this->table->getRight();

		return $this->mapper->fetchSinglePath($id, $pk, $left, $right);
	}

	/**
	 * Add child
	 *
	 * @param mixed $parent tree item id
	 * @param array|NULL $id key-value array [column-name] => value 
	 *   left and right side'll be added automatically 
	 * @return array 
	 */
	public function addChild($parent, $id) {
		$pk = $this->table->getPrimaryKey();
		$field_left = $this->table->getLeft();
		$field_right = $this->table->getRight();

		$parent_row = $this->mapper->fetchByField($pk, $parent);
		$parent_lft = $parent_row[$field_left];
		$lft = $parent_lft + 1;
		$rgt = $parent_lft + 2;

		if (!is_array($id)) {
			$id = array();
		}

		$this->addSides($id, $lft, $rgt);

		$this->mapper->addNToFieldGreaterThan($field_left, 2, $parent_lft);
		$this->mapper->addNToFieldGreaterThan($field_right, 2, $parent_lft);
		$id[$pk] = $this->mapper->insert($id);

		return $id;
	}

	public function addSibling($sibling_id, $id) {
		$primary_key = $this->table->getPrimaryKey();
		$field_left = $this->table->getLeft();
		$field_right = $this->table->getRight();

		$sibling_row = $this->mapper->fetchByField($primary_key, $sibling_id);
		$sibling_rgt = $sibling_row[$field_right];

		$lft = $sibling_rgt + 1;
		$rgt = $sibling_rgt + 2;

		if (!is_array($id)) {
			$id = array();
		}

		$this->addSides($id, $lft, $rgt);

		$this->mapper->addNToFieldGreaterThan($field_left, 2, $sibling_rgt);
		$this->mapper->addNToFieldGreaterThan($field_right, 2, $sibling_rgt);
		$id[$primary_key] = $this->mapper->insert($id);
		return $id;
	}

	public function removeNode($id) {
		$primary_key = $this->table->getPrimaryKey();
		$field_left = $this->table->getLeft();
		$field_right = $this->table->getRight();

		$row = $this->mapper->fetchByField($primary_key, $id);

		$lft = $row[$field_left];
		$rgt = $row[$field_right];
		$width = ($rgt - $lft) + 1;

		$this->mapper->deleteBetweenField($field_left, $lft, $rgt);
		$this->mapper->subtractNToFieldGreaterThan($field_left, $width, $rgt);
		$this->mapper->subtractNToFieldGreaterThan($field_right, $width, $rgt);
		
		return TRUE;
	}

	/**
	 * Add sides to array
	 *
	 * @param array $node reference to node [column name] => value
	 * @param int $left
	 * @param int $right
	 * @return void
	 */
	private function addSides(array & $node, $left, $right) {
		$node[$this->table->getLeft()] = $left;
		$node[$this->table->getRight()] = $right;
	}
}
