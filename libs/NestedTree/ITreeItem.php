<?php
/**
 * @author Petr Kramar
 * @license MIT
 */
namespace NestedTree;

interface ITreeItem {
	/**
	 * Is leaf? (last node in branch)
	 * @return boolean
	 */
	public function isLeaf();

	/**
	 * Get data
	 * @return mixed
	 */
	public function getData();

	/**
	 * get parent
	 * @return ITreeItem|NULL
	 */
	public function getParent();

	/**
	 * set parent
	 * @param ITreeItem $parent
	 * @return void
	 */
	public function setParent(ITreeItem $parent);
}
