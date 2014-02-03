<?php
/** 
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT 
 */

namespace NestedTree;

interface ITree {
	/**
	 * Set table 
	 * @param ITable $table
	 */
	function setTable(ITable $table);

	/**
	 * Set PDO connection
	 * @param \PDO $conn
	 */
	function setConnection(\PDO $conn);

	/**
	 * Get whole tree
	 * @return \PDOStatement
	 */
	function getTree();

	/**
	 * Get subtree
	 * @param mixed $id
	 * @return \PDOStatement
	 */
	function getSubTree($id);

	/**
	 * Get single path
	 * @param mixed $id identifier
	 * @return \PDOStatement
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
