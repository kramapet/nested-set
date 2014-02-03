<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT
 */

namespace NestedTree;

class Table implements ITable {
	/** @var string */
	private $name;
	/** @var string */
	private $primary_key;
	/** @var string */
	private $left;
	/** @var string */
	private $right;

	/**
	 * Constructor initalize required properties
	 * @throws \Exception when empty or non-string variable is passed in or 
	 *                    total count of arguments is not equal to 4
	 */
	public function __construct($name, $pk, $lft, $rgt) {
		if (func_num_args() != 4) {
			throw new \Exception("Count of arguments passed in constructor must be equal to 4");
		}

		foreach (func_get_args() as $a) {
			if (!is_string($a) || $a == "") {
				throw new \Exception("Argument must be non-empty string.");
			}
		}

		$this->name = $name;
		$this->primary_key = $pk;
		$this->left = $lft;
		$this->right = $rgt;
	}


	/** interface ITable */
	public function getTableName() {
		return $this->name;
	}

	public function getPrimaryKey() {
		return $this->primary_key;
	}

	public function getLeft() {
		return $this->left;
	}

	public function getRight() {
		return $this->right;
	}
}
