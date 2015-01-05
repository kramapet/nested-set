<?php
/**
 * @author Petr Kramar
 * @license MIT 
 */

namespace NestedSet; 

class TreeItem implements ITreeItem {

	/** @var ITreeItem */
	private $parent;
	/** @var mixed */
	private $data;
	/** @var ITreeItem[] */
	private $childs;

	public function __construct(ITreeItem $parent = NULL, $data = NULL) {
		if ($parent !== NULL) {
			$this->setParent($parent);
		}

		$this->childs = array();
		$this->data = $data;
	}

	public function addChild(ITreeItem $child) {
		if ($this == $child) { 
			throw new \Exception("Cannot set yourself as a child");
		}

		$this->childs[] = $child;
	}

	public function isLeaf() {
		return count($this->childs) == 0;
	}

	public function getChilds() {
		return $this->childs;
	}

	public function getData() {
		return $this->data;
	}

	public function getParent() {
		return $this->parent;
	}

	public function setParent(ITreeItem $parent) {
		$this->parent = $parent;
		$parent->addChild($this);
	}
}
