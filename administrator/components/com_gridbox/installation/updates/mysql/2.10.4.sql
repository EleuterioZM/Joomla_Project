DROP TABLE IF EXISTS `#__gridbox_countries`;
CREATE TABLE `#__gridbox_countries` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255),
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_country_states`;
CREATE TABLE `#__gridbox_country_states` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `country_id` varchar(255),
    `title` varchar(255),
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;