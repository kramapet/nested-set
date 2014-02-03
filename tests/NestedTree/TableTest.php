<?php
/**
 * NestedTree\Table test
 */
namespace Tests\NestedTree;


use \NestedTree\Table;

class TableTest extends \PHPUnit_Framework_TestCase {
	public function testTable() {
		$tbl = new Table('tree', 'pk', 'left', 'right');

		$this->assertEquals('tree', $tbl->getTableName());
		$this->assertEquals('pk', $tbl->getPrimaryKey());
		$this->assertEquals('left', $tbl->getLeft());
		$this->assertEquals('right', $tbl->getRight());
	}

	/**
	 * @expectedException \Exception
	 */
	public function testTableTooFewArguments() {
		new Table('adas', 'asdsad', 'adsad');
	}
}
