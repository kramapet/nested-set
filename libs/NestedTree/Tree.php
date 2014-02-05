<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT
 */

namespace NestedTree;


class Tree implements ITree {
	
	/** @var \PDO */
	private $conn;
	/** @var ITable */
	private $table;

	public function __construct(\PDO $conn, ITable $table) {
		$this->setTable($table);
		$this->setConnection($conn);
	}

	/* interface ITree */
	public function setTable(ITable $table) {
		if ($this->table instanceof ITable) {
			throw new \Exception("Table is already set.");
		}

		$this->table = $table;

		return $this;
	}

	public function setConnection(\PDO $conn) {
		if ($this->conn instanceof \PDO) {
			throw new \Exception("Connection is already set.");
		}

		$this->conn = $conn;

		return $this;
	}

	public function getTree() {
		$sql = "SELECT * FROM {$this->table->getTableName()}";

		return $this->conn->query($sql);
	}

	public function getSubtree($id) {
		$tblName = $this->table->getTableName();
		$pk = $this->table->getPrimaryKey();
		$lftCol = $this->table->getLeft();
		$rgtCol = $this->table->getRight();
		$sql = "SELECT $lftCol, $rgtCol FROM $tblName WHERE $pk = " . $this->conn->quote($id);

		$id_res = $this->conn->query($sql)->fetch();
		$id_lft = $this->conn->quote($id_res[$lftCol]);
		$id_rgt = $this->conn->quote($id_res[$rgtCol]);
		
		$sql2 = "SELECT * FROM $tblName WHERE lft BETWEEN $id_lft AND $id_rgt";
		return $this->conn->query($sql2);
	}

	public function getSinglePath($id) {
		$tblName = $this->table->getTableName();
		$pk = $this->table->getPrimaryKey();
		$lftCol = $this->table->getLeft();
		$rgtCol = $this->table->getRight();

		$sql = "SELECT parent.* 
		        FROM {$tblName} as parent, {$tblName} as node
				WHERE node.{$lftCol} between parent.{$lftCol} and parent.{$rgtCol}
				AND node.{$pk} = " . $this->conn->quote($id);

		return $this->conn->query($sql);
	}

	public function addChild($parent, $id) {
		$tblName = $this->table->getTableName();
		$pk = $this->table->getPrimaryKey();
		$lftCol = $this->table->getLeft();
		$rgtCol = $this->table->getRight();

		try {

			$this->conn->beginTransaction();
			$st = $this->conn->query("SELECT $lftCol FROM $tblName WHERE $pk = " . $this->conn->quote($parent));
			$row = $st->fetch();

			$parent_lft = $row[$lftCol];

			$id = $this->conn->quote($id);
			$lft = $parent_lft + 1;
			$rgt = $parent_lft + 2;

			$this->conn->query("UPDATE $tblName SET $lftCol = $lftCol + 2 WHERE $lftCol > " . $this->conn->quote($parent_lft));
			$this->conn->query("UPDATE $tblName SET $rgtCol = $rgtCol + 2 WHERE $rgtCol > " . $this->conn->quote($parent_lft));
			$this->conn->query("INSERT INTO $tblName ($pk, $lftCol, $rgtCol) VALUES ($id, $lft, $rgt)");
			$this->conn->commit();
			return true;

		} catch (\PDOException $pe) {
			$this->conn->rollback();
			return false;
		}
	}

	public function addSibling($sibling, $id) {
		$tblName = $this->table->getTableName();
		$pk = $this->table->getPrimaryKey();
		$lftCol = $this->table->getLeft();
		$rgtCol = $this->table->getRight();

		try {
			$this->conn->beginTransaction();

			$res = $this->conn->query("SELECT $rgtCol FROM $tblName WHERE $pk = " . $this->conn->quote($sibling));
			$row = $res->fetch();

			$sibling_rgt = $this->conn->quote($row[$rgtCol]);

			$id_quoted = $this->conn->quote($id);
			$id_lft = $sibling_rgt + 1;
			$id_rgt = $sibling_rgt + 2;
			
			$this->conn->query("UPDATE $tblName SET $lftCol = $sibling_rgt + 2 WHERE $lftCol > $sibling_rgt");
			$this->conn->query("UPDATE $tblName SET $rgtCol = $sibling_rgt + 2 WHERE $rgtCol > $sibling_rgt");
			$this->conn->query("INSERT INTO $tblName ($pk, $lftCol, $rgtCol) VALUES ($id_quoted, $id_lft, $id_rgt)");

			$this->conn->commit();

			return true;
		} catch (\PDOException $pe) {

			$this->conn->rollback();
			return false;
		}
	}

	public function removeNode($id) {
		$tblName = $this->table->getTableName();
		$pk = $this->table->getPrimaryKey();
		$lftCol = $this->table->getLeft();
		$rgtCol = $this->table->getRight();
		
		try {
			$this->conn->beginTransaction();
				
			$id_values = $this->conn->query("SELECT $lftCol, $rgtCol FROM $tblName WHERE $pk = " . $this->conn->quote($id))->fetch();
			$id_lft = $this->conn->quote($id_values[$lftCol]);
			$id_rgt = $this->conn->quote($id_values[$rgtCol]);
			$id_width = $this->conn->quote(($id_rgt - $id_lft) + 1);

			$this->conn->query("DELETE FROM $tblName WHERE $lftCol BETWEEN $id_lft AND $id_rgt");
			$this->conn->query("UPDATE $tblName SET $lftCol = $lftCol - $id_width WHERE $lftCol > $id_rgt");
			$this->conn->query("UPDATE $tblName SET $rgtCol = $rgtCol - $id_width WHERE $rgtCol > $id_rgt");
			$this->conn->commit();
			return true;
		} catch (\PDOException $pe) {
			$this->conn->rollback();
			return false;
		}

	}

}
