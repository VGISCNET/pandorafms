START TRANSACTION;

CREATE TABLE IF NOT EXISTS `ttable_test3` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`field1` varchar(60) NOT NULL default '',
	`field2` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `tusuario` ADD COLUMN `test3` tinyint(1) NOT NULL DEFAULT 0;

COMMIT;