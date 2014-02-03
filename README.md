Nested Tree for PDO
===================
REF: http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/

$ sqlite3  db/test_tree.sqlite 
SQLite version 3.6.20
Enter ".help" for instructions
Enter SQL statements terminated with a ";"
sqlite> .mode tabs
sqlite> .headers on
sqlite> .dump tree
PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE `tree` (
	`name` char(20) not null,
	`parent` char(20) not null,
	`lft` int unsigned not null,
	`rgt` int unsigned not null,
	primary key (`name`)
);
INSERT INTO "tree" VALUES('Alpha','',1,16);
INSERT INTO "tree" VALUES('Beta','Alpha',2,9);
INSERT INTO "tree" VALUES('Gamma','Alpha',10,15);
INSERT INTO "tree" VALUES('Delta','Beta',3,4);
INSERT INTO "tree" VALUES('Epsilon','Beta',5,6);
INSERT INTO "tree" VALUES('Zeta','Beta',7,8);
INSERT INTO "tree" VALUES('Eta','Gamma',11,12);
INSERT INTO "tree" VALUES('Theta','Gamma',13,14);
COMMIT;
sqlite> select * from tree;
name	parent	lft	rgt
Alpha		1	16
Beta	Alpha	2	9
Gamma	Alpha	10	15
Delta	Beta	3	4
Epsilon	Beta	5	6
Zeta	Beta	7	8
Eta	Gamma	11	12
Theta	Gamma	13	14
sqlite> 


<?php
use NestedTree\Table;
use NestedTree\Tree;

// pdo connection
$pdo = new PDO('sqlite:/tmp/test_tree.sqlite');

// nested tree initialization
$table = new Table('tree', 'name', 'lft', 'rgt'); 
$tree = new Tree($pdo, $table);

foreach ($tree->getSubtree('Beta') as $row) {
	var_dump($row);
}
