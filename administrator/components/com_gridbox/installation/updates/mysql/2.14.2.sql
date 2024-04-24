ALTER TABLE `#__gridbox_website` ADD `versions_auto_save` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `max_versions` varchar(255) NOT NULL DEFAULT '10';

DROP TABLE IF EXISTS `#__gridbox_pages_versions`;
CREATE TABLE `#__gridbox_pages_versions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `page_id` int(11) NOT NULL DEFAULT 0,
    `html` mediumtext,
    `items` mediumtext,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;