<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT
 */
namespace NestedTree;

interface ITable {
	/**
	 * Get table name
	 * @return string
	 */
	function getTableName();	

	/**
	 * Get name of primary key
	 * @return string
	 */
	function getPrimaryKey();

	/**
	 * Get column name of left side
	 * @return string
	 */
	function getLeft();

	/**
	 * Get column name of right side
	 * @return string
	 */
	function getRight();
}
