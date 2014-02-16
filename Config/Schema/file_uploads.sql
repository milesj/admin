
DROP TABLE IF EXISTS `{prefix}file_uploads`;

CREATE TABLE `{prefix}file_uploads` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `caption` text NOT NULL,
    `path` varchar(255) NOT NULL,
    `path_thumb` varchar(255) NOT NULL,
    `path_large` varchar(255) NOT NULL,
    `size` int(11) NOT NULL,
    `ext` varchar(10) NOT NULL,
    `type` varchar(50) NOT NULL,
    `width` smallint(6) DEFAULT NULL,
    `height` smallint(6) DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;