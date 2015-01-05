<?php
/**
 * @author Petr Kramar
 * @license MIT
 */
namespace NestedSet;

interface ITreeItem {
	/**
	 * Add child
	 * @param ITreeItem $child
	 * @return void
	 */
	public function addChild(ITreeItem $child);

	/**
	 * get childs
	 * @return ITreeItem[]|NULL
	 */
	public function getChilds();
		
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
	 * Is leaf? (last node in branch)
	 * @return boolean
	 */
	public function isLeaf();


	/**
	 * set parent
	 * @param ITreeItem $parent
	 * @return void
	 */
	public function setParent(ITreeItem $parent);
}
