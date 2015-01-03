<?php
/** 
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT 
 */

namespace NestedTree;

interface ITree {
	/**
	 * Get whole tree
	 * @return array|\Traversable
	 */
	function getTree();

	/**
	 * Get subtree
	 * @param mixed $id
	 * @return array|\Traversable
	 */
	function getSubTree($id);

	/**
	 * Get single path
	 * @param mixed $id identifier
	 * @return array|\Traversable
	 */
	function getSinglePath($id);
	
	/**
	 * Add node 
	 * @param mixed|NULL $parent
	 * @param mixed $id
	 * @return boolean
	 */
	function addChild($parent, $id);

	/**
	 * Add sibling
	 * @param mixed|NULL $sibling
	 * @param mixed $id
	 * @return boolean
	 */
	function addSibling($sibling, $id);

	/**
	 * Remove node with its childs
	 * @param mixed $id
	 * @return boolean
	 */
	function removeNode($id);
}
