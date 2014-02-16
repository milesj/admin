
DROP TABLE IF EXISTS `{prefix}action_logs`;

CREATE TABLE `{prefix}action_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `action` smallint(6) NOT NULL DEFAULT '0',
    `model` varchar(100) DEFAULT NULL,
    `foreign_key` varchar(36) DEFAULT NULL,
    `item` varchar(255) DEFAULT NULL,
    `comment` varchar(255) DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;