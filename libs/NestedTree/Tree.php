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
		$row = $this->mapper->fetchByField($this->table->getPrimaryKey(), $id);

		return $this->mapper->fetchBetweenField(
			$this->table->getLeft(), 
			$row[$this->table->getLeft()],
			$row[$this->table->getRight()]
		);
	}

	public function getSinglePath($id) {
		return $this->mapper->fetchSinglePath(
			$id,
			$this->table->getPrimaryKey(),
			$this->table->getLeft(),
			$this->table->getRight()
		);
	}

	/**
	 * Add child
	 *
	 * @param mixed $parent tree item id
	 * @param array|NULL $id key-value array [column-name] => value 
	 *   left and right side'll be added automatically 
	 * @return bool TRUE on success
	 */
	public function addChild($parent, $id) {
		$parent_row = $this->mapper->fetchByField($this->table->getPrimaryKey(), $parent);
		$parent_lft = $parent_row[$this->table->getLeft()];
		$lft = $parent_lft + 1;
		$rgt = $parent_lft + 2;

		if (!is_array($id)) {
			$id = array();
		}

		$this->addSides($id, $lft, $rgt);

		$this->mapper->addNToFieldGreaterThan($this->table->getLeft(), 2, $parent_lft);
		$this->mapper->addNToFieldGreaterThan($this->table->getRight(), 2, $parent_lft);
		$this->mapper->insert($id);
	}

	public function addSibling($sibling_id, $id) {
		$sibling_row = $this->mapper->fetchByField(
			$this->table->getPrimaryKey(), 
			$sibling_id
		);

		$sibling_rgt = $sibling_row[$this->table->getRight()];

		$lft = $sibling_rgt + 1;
		$rgt = $sibling_rgt + 2;

		if (!is_array($id)) {
			$id = array();
		}

		$this->addSides($id, $lft, $rgt);

		$this->mapper->setFieldGreaterThan(
			$this->table->getLeft(), 
			$sibling_rgt + 2, 
			$sibling_rgt
		);

		$this->mapper->setFieldGreaterThan(
			$this->table->getRight(),
			$sibling_rgt + 2,
			$sibling_rgt
		);

		$this->mapper->insert($id);
		return TRUE;
	}

	public function removeNode($id) {
		$row = $this->mapper->fetchByField($this->table->getPrimaryKey(), $id);

		$lft = $row[$this->table->getLeft()];
		$rgt = $row[$this->table->getRight()];
		$width = ($rgt - $lft) + 1;

		$this->mapper->deleteBetweenField($this->table->getLeft(), $lft, $rgt);
		$this->mapper->subtractNToFieldGreaterThan($this->table->getLeft(), $width, $rgt);
		$this->mapper->subtractNToFieldGreaterThan($this->table->getRight(), $width, $rgt);
		
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
