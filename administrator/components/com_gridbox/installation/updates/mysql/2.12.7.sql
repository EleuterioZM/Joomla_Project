DROP TABLE IF EXISTS `#__gridbox_associations`;
CREATE TABLE `#__gridbox_associations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11),
    `item_type` varchar(255) NOT NULL DEFAULT "",
    `hash` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_customer_info_data`;
CREATE TABLE `#__gridbox_store_customer_info_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11),
    `field_id` int(11),
    `title` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `options` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__gridbox_system_pages` ADD `language` varchar(255) NOT NULL DEFAULT "*";
ALTER TABLE `#__gridbox_system_pages` ADD `published` tinyint(1) NOT NULL DEFAULT 1;