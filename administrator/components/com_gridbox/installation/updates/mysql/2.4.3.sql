DROP TABLE IF EXISTS `#__gridbox_page_blocks`;
CREATE TABLE `#__gridbox_page_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `item` mediumtext NOT NULL,
    `image` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;