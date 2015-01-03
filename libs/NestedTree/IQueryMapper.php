<?php
/**
 * @author Petr Kramar <kramar@masquerade.cz>
 * @license MIT
 */

namespace NestedTree;

interface IQueryMapper {
	/**
	 * Fetch all records
	 *
	 * @return array|Traversable
	 */
	function fetchAll();

	/**
	 * Fetch all records where left side is between
	 *
	 * @param string $field
	 * @param int $left
	 * @param int $right
	 * @return array|Traversable
	 */
	function fetchBetweenField($field, $left, $right);

	/**
	 * Fetch record by id
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return array
	 */
	function fetchByField($field, $value);

	/**
	 * Fetch records to get single path to id
	 *
	 * @param mixed $id
	 * @param string $pk column name of primary key
	 * @param string $left column name of left side
	 * @param string $right column name of right side
	 * @return array|Traversable
	 */
	function fetchSinglePath($id, $pk, $left, $right);

	/**
	 * All record with $field > $greaterThan set to $value
	 *
	 * @param string $field
	 * @param int $value
	 * @param int $greaterThan
	 * @return int affected rows
	 * @throws TreeManipulationFailedException
	 */
	function setFieldGreaterThan($field, $value, $greaterThan);

	/**
	 * All records with column specified by $field will be added by $n 
	 * if $field > $greaterThan
	 * 
	 * SQL: UPDATE {table} SET {field} = {field} + {n} WHERE {field} > {greaterThan}
	 *
	 * @param string $field
	 * @param int $n 
	 * @param int $greaterThan
	 * @return void
	 * @throws TreeManipulationFailedException
	 */
	function addNToFieldGreaterThan($field, $n, $greaterThan);

	/**
	 * All records with column specified by $field will be subtracted by $n 
	 * if $field > $greaterThan
	 * 
	 * SQL: UPDATE {table} SET {field} = {field} - {n} WHERE {field} > {greaterThan}
	 *
	 * @param string $field
	 * @param int $n 
	 * @param int $greaterThan
	 * @return void
	 * @throws TreeManipulationFailedException
	 */
	function subtractNToFieldGreaterThan($field, $n, $greaterThan);

	/**
	 * Delete records where left side is between $left and $right
	 *
	 * @param string $field
	 * @param int $left
	 * @param int $right
	 * @throws TreeManipulationFailedException
	 */
	function deleteBetweenField($field, $left, $right);

	/**
	 * Insert record
	 *
	 * @param array $fields [column name] => value
	 * @throws TreeManipulationFailedException
	 */
	function insert(array $fields);
}
