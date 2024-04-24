DROP TABLE IF EXISTS `#__gridbox_authors`;
CREATE TABLE `#__gridbox_authors` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `alias` varchar(255) NOT NULL,
    `hits` int(11) NOT NULL DEFAULT 0,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `avatar` varchar(255) NOT NULL DEFAULT '',
    `description` text NOT NULL,
    `image` varchar(255) NOT NULL,
    `meta_title` varchar(255) NOT NULL,
    `meta_description` text NOT NULL,
    `meta_keywords` text NOT NULL,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `author_social` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_authors_map`;
CREATE TABLE `#__gridbox_authors_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `author_id` int(11) NOT NULL DEFAULT 0,
    `page_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `#__gridbox_website` ADD `compress_images` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `images_max_size` varchar(255) NOT NULL DEFAULT '1440';
ALTER TABLE `#__gridbox_website` ADD `images_quality` varchar(255) NOT NULL DEFAULT '60';