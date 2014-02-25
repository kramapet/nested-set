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
		// $items[rgt] => id
		$items = $rgt_indexes = $lft_indexes = array();
		$last = $root = $prev_lft = $prev_rgt = NULL;
		$firstLoop = TRUE;

		foreach ($data as $k => $v) { // $v [0]=>data, [1]=>lft, [2]=>rgt
			$lft_indexes[$k] = $v[self::LEFT]; // [id] => left
			$rgt_indexes[$k] = $v[self::RIGHT]; // [id] => right
		}

		asort($lft_indexes);
		asort($rgt_indexes);

		foreach ($lft_indexes as $index => $lft) {
			$item = $data[$index];
			$rgt = $rgt_indexes[$index];

			if ($firstLoop) { // root node
				$firstLoop = FALSE;
				$items[$rgt] = $last = $root = $this->createItem(NULL, $item[self::DATA]);

			} elseif ($this->isChild($lft, $prev_lft)) {
				$items[$rgt] = $last = $this->createItem($last, $item[self::DATA]);

			} elseif ($this->hasSibling($lft, $items)) {
				$items[$rgt] = $last = $this->createItem(
					$this->getSibling($lft, $items)->getParent(),
					$item[self::DATA]
				);
			} else {
				throw new \Exception("Invalid tree");
			}

			$prev_lft = $item[self::LEFT];
			$prev_rgt = $item[self::RIGHT];
		}

		return $root;
	}

	private function createItem($parent, $data) {
		return new TreeItem($parent, $data);
	}

	private function isLeaf($lft, $rgt) {
		return ($lft + 1) == $rgt;
	}

	private function isChild($lft, $prev_lft) {
		return ($lft - 1) == $prev_lft;
	}

	private function hasSibling($lft, array & $items_by_rgt) {
		$rgt = $lft - 1;
		return isset($items_by_rgt[$rgt]);
	}

	private function getSibling($lft, array & $items_by_rgt) {
		$rgt = $lft - 1;
		if (!isset($items_by_rgt[$rgt])) 
			return FALSE;

		return $items_by_rgt[$rgt];
	}
}
