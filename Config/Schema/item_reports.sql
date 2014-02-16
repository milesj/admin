
DROP TABLE IF EXISTS `{prefix}item_reports`;

CREATE TABLE `{prefix}item_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `reporter_id` int(11) DEFAULT NULL,
    `resolver_id` int(11) DEFAULT NULL,
    `status` smallint(6) NOT NULL DEFAULT '0',
    `type` smallint(6) NOT NULL DEFAULT '0',
    `model` varchar(100) DEFAULT NULL,
    `foreign_key` varchar(36) DEFAULT NULL,
    `item` varchar(255) DEFAULT NULL,
    `reason` text,
    `comment` text,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `reporter_id` (`reporter_id`),
    KEY `resolver_id` (`resolver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;