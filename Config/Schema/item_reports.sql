
DROP TABLE IF EXISTS `{prefix}item_reports`;

CREATE TABLE `{prefix}item_reports` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`model` varchar(35) NOT NULL,
	`foreign_key` int(11) DEFAULT NULL,
	`item` varchar(255) DEFAULT NULL,
	`comment` varchar(255) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;