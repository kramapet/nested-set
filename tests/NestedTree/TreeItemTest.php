<?php
/**
 * NestedTree\TreeItem test
 */

use \NestedTree\TreeItem;

class TreeItemTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
	}

	public function testAddChild() {
		$parent = new TreeItem(NULL, 'parent');
		$child = new TreeItem($parent, 'child');

		$childs = $parent->getChilds();
		$this->assertEquals($child, $childs[0]);

	} 

	public function testIsLeaf() {
		$item = new TreeItem();
		$this->assertTrue($item->isLeaf());

		$item2 = new TreeItem($item);
		$this->assertFalse($item->isLeaf());
	}

	public function testGetData() {
		$item = new TreeItem();
		$this->assertEquals($item->getData(), NULL);

		$d = array('name' => 'Hello', 'lft' => 1, 'rgt' => 16);
		$item2 = new TreeItem(NULL, $d);
		$this->assertEquals($item2->getData(), $d);

	}

	public function testGetParent() {
		$parent = new TreeItem();
		$this->assertEquals($parent->getParent(), NULL);

		$child = new TreeItem($parent);
		$this->assertEquals($child->getParent(), $parent);
	}

	public function testSetParent() {
		$parent = new TreeItem();
		$child = new TreeItem();
		$child->setParent($parent);

		$this->assertEquals($child->getParent(), $parent);
	}
}
