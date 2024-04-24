DROP TABLE IF EXISTS `#__gridbox_custom_user_icons`;
CREATE TABLE `#__gridbox_custom_user_icons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `group` varchar(255) NOT NULL,
    `path` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;