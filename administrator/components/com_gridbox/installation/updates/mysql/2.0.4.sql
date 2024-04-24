DROP TABLE IF EXISTS `#__gridbox_star_ratings`;
CREATE TABLE IF NOT EXISTS  `#__gridbox_star_ratings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL,
    `option` varchar(255) NOT NULL,
    `view` varchar(255) NOT NULL,
    `page_id` varchar(255) NOT NULL,
    `rating` FLOAT NOT NULL,
    `count` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_star_ratings_users`;
CREATE TABLE IF NOT EXISTS  `#__gridbox_star_ratings_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL,
    `option` varchar(255) NOT NULL,
    `view` varchar(255) NOT NULL,
    `page_id` varchar(255) NOT NULL,
    `ip` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;