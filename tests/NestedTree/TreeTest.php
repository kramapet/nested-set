<?php

namespace Tests\NestedTree;

use \NestedTree\Tree;
use \NestedTree\Table;

class TreeTest extends BaseTree_TestCase {

	/** @var Tree */
	private $tree;
	
	protected function setUp() {
		$table = new Table('tree', 'name', 'lft', 'rgt');
		$this->tree = new Tree(self::getPdo(), $table);
	}

	public function testGetTree() {
		$expected_name = array(
			'alpha' => true,
			'beta' => true,
			'gamma' => true,
			'delta' => true,
			'eta' => true,
			'epsilon' => true,
			'zeta' => true,
			'theta' => true
		);

		foreach ($this->tree->getTree() as $r) {
			$this->assertTrue($expected_name[$r['name']]);
		}
	}

	public function testGetSubtree() {
		$expected_values = array('beta' => true, 'delta' => true, 'zeta' => true, 'epsilon' => true);
		foreach ($this->tree->getSubtree('beta') as $r) {
			$this->assertTrue($expected_values[$r['name']]); 
		}
	}
	
	public function testSinglePath() {
		$expected = array('alpha', 'gamma', 'eta');
		$result = array();
		foreach ($this->tree->getSinglePath('eta') as $r) {
			$result[] = $r['name'];
		}

		$this->assertEquals($expected, $result);
	}

	public function testAddChild() {
		$parent = 'eta';
		$id = 'foo';

		$this->tree->addChild($parent, $id);
		
		$expected_values = array('alpha' => true, 'gamma' => true, 'eta' => true, 'foo' => true);
		foreach ($this->tree->getSinglePath('foo') as $r) {
			$this->assertTrue($expected_values[$r['name']]);
		}
	}
	
}
