<?php
/**
 * NestedTree\Factory test
 * @author Petr Kramar
 * @license MIT
 */

namespace Tests\NestedTree;

use \NestedTree\Factory;
use \NestedTree\ITreeItem;

class FactoryTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->factory = new Factory();
	}
	
	public function testCreateTree() {
		$tree = $this->factory->createTree($this->getData());
		$this->assertTrue($tree instanceof ITreeItem);
		$childs = $tree->getChilds();
		$this->assertNotNull($childs);

		$this->assertEquals($tree->getData(), 'alpha');
		$this->assertEquals($childs[0]->getData(), 'beta');
		$this->assertEquals($childs[1]->getData(), 'gamma');

		$beta_childs = $childs[0]->getChilds();
		$this->assertEquals($beta_childs[0]->getData(), 'delta');
		$this->assertEquals($beta_childs[1]->getData(), 'epsilon');
	}

	private function getData() {
		$data = array();
		$dataset_path = __DIR__ . '/datasets/treeDb.xml';
		$xml = simplexml_load_file($dataset_path);
		foreach ($xml->tree as $t) {
			$data[] = array(
				(string) $t['name'], 
				(string) $t['lft'], 
				(string) $t['rgt']
			);
		}

		return $data;
	}
}
