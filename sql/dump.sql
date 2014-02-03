drop table if exists `tree`;
create table `tree` (
	`name` char(20) not null,
	`parent` char(20) not null,
	`lft` int unsigned not null,
	`rgt` int unsigned not null,
	primary key (`name`)
);

insert into `tree` values ('Alpha', '', 1, 16);
insert into `tree` values ('Beta', 'Alpha', 2, 9);
insert into `tree` values ('Gamma', 'Alpha', 10, 15);
insert into `tree` values ('Delta', 'Beta', 3, 4);
insert into `tree` values ('Epsilon', 'Beta', 5, 6);
insert into `tree` values ('Zeta', 'Beta', 7, 8);
insert into `tree` values ('Eta', 'Gamma', 11, 12);
insert into `tree` values ('Theta', 'Gamma', 13, 14);
