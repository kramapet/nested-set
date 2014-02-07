<?php
/**
 * @author Petr Kramar
 * @license MIT
 */

namespace NestedTree;

class Factory {
	const DATA = 0;
	const LEFT = 1;
	const RIGHT = 2;

	public function createTree(array $data) {
		$lft_indexes = array();
		foreach ($data as $k => $v) // $v [0]=>data, [1]=>lft, [2]=>rgt
			$lft_indexes[$k] = $v[self::LEFT]; // [data] => lft

		asort($lft_indexes);

		$parent = NULL;
		$prev_lft = NULL;
		$prev_rgt = NULL;
	
		
		foreach ($lft_indexes as $index => $_lft) {
			$item = $data[$index];

			if ($lft == 1) { // parent
				$last = $parent = $this->createItem(NULL, $item[0]);
			} else {
				// todo: finish it!
			}

			$prev_lft = $item[self::LEFT];
			$prev_rgt = $item[self::RIGHT];
		}
	}

	private function createItem($parent, $data) {
		return new TreeItem($parent, $data);
	}

	private function isLeaf($lft, $rgt) {
		return ($lft + 1) == $rgt;
	}
}
