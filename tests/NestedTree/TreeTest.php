<?php

namespace Tests\NestedTree;

use \NestedTree\Tree;
use \NestedTree\Table;

class TreeTest extends BaseTree_TestCase {

	/** @var Tree */
	private $tree;
	
	protected function setUp() {
		parent::setUp();
		$this->tree = $this->createTree();
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
		$beta_id = 2;
		$expected_values = array(
			'beta' => true, 
			'delta' => true, 
			'zeta' => true, 
			'epsilon' => true
		);

		foreach ($this->tree->getSubtree($beta_id) as $r) {
			$this->assertTrue($expected_values[$r['name']]); 
		}
	}
	
	public function testSinglePath() {
		$expected = array('alpha', 'gamma', 'eta');
		$result = array();
		foreach ($this->tree->getSinglePath(7) as $r) {
			$result[] = $r['name'];
		}


		$this->assertEquals($expected, $result);
	}

	public function testAddChild() {
		$parent = 7; // eta
		$new['name'] = 'foo';

		$new = $this->tree->addChild($parent, $new);

		$expected_values = array(
			'alpha' => true, 
			'gamma' => true, 
			'eta' => true, 
			'foo' => true
		);

		foreach ($this->tree->getSinglePath($new['id']) as $r) {
			$this->assertTrue($expected_values[$r['name']]);
		}
	}

	public function testAddSibling() {
		$sibling_id = 2;
		$new['name'] = 'foo';

		$new = $this->tree->addSibling($sibling_id, $new);

		$pdo = self::getPdo();
		$sibling_id = $pdo->quote($sibling_id);
		$sql = "SELECT rgt FROM tree WHERE id = $sibling_id";
		$sibling_row = $pdo->query($sql)->fetch();

		$this->assertEquals($sibling_row['rgt'], $new['lft'] - 1);
	}

	public function testRemoveNode() {
		$id = 'beta';
		$this->assertTrue($this->tree->removeNode($id));
	}
	
}
