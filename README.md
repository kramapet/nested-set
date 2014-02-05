Nested Tree for PDO
===================
REF: http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/


Install:
- clone git repository
- change directory into repository
- run 'composer install'

Usage:
	<?php

	use \NestedTree;

	$pdo = new \PDO($dsn);

	$table = new NestedTree\Table('table_name', 'primary_key', 'colname_of_left_index', 'colname_of_right_index');
	$tree = new NestedTree\Tree($pdo, $table);



