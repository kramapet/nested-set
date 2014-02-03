--
-- delete table if exists and create it again
--

drop table if exists `tree`;

create table `tree` (
	`name` char(20) not null,
	`parent` char(20) not null,
	`lft` int unsigned not null,
	`rgt` int unsigned not null,
	primary key (`name`)
)engine=InnoDB;

--
-- insert base tree
--

insert into `tree` (`name`, `parent`, `lft`, `rgt` ) values
('Alpha', null, 1, 16),
('Beta', 'Alpha', 2, 9),
('Gamma', 'Alpha', 10, 15),
('Delta', 'Beta', 3, 4),
('Epsilon', 'Beta', 5, 6),
('Zeta', 'Beta', 7, 8),
('Eta', 'Gamma', 11, 12),
('Theta', 'Gamma', 13, 14);

-- 
-- retrieve subtree
--

select @lftNode := `lft`, @rgtNode := `rgt` from `tree` where `name` = 'Beta';
select * from `tree` where `lft` > @lftNode and `rgt` < @rgtNode;

--
-- retrieve single path
--

set @name = 'Theta';

select `parent`.*
from `tree` as `parent`, `tree` as `node`
where `node`.`name` = @name 
and `node`.`lft` between `parent`.`lft` and `parent`.`rgt`;

--
-- add child 'Omega'
--

start transaction;

select @lftparent := `lft` from `tree` where `name` = 'Delta';

update `tree` set `rgt` = `rgt` + 2 where `rgt` > @lftParent;
update `tree` set `lft` = `lft` + 2 where `lft` > @lftParent;

insert into `tree` (`name`, `parent`, `lft`, `rgt`) values ('Omega', 'Delta', @lftParent + 1, @lftParent + 2);

commit;

--
-- add child 'Psi'
--

start transaction;

select @lftParent := `lft` FROM `tree` WHERE `name` = 'Alpha';

update `tree` set `rgt` = `rgt` + 2 where `rgt` > @lftParent;
update `tree` set `lft` = `lft` + 2 where `lft` > @lftParent;

insert into `tree` (`name`, `parent`, `lft`, `rgt`) values ('Psi', 'Alpha', @lftParent + 1, @lftParent + 2);

commit;

--
-- delete node 'Beta' with its childs
--

start transaction;

select @lftNode := `lft`, @rgtNode := `rgt`, @widthNode := (@rgtNode - @lftNode) + 1 from `tree` where `name` = 'Beta';

delete from `tree` where `lft` between @lftNode and @rgtNode;

update `tree` set `lft` = `lft` - @widthNode where `lft` > @rgtNode;
update `tree` set `rgt` = `rgt` - @widthNode where `rgt` > @rgtNode;

commit;

--
-- print table `tree`
--

select * from `tree`;
