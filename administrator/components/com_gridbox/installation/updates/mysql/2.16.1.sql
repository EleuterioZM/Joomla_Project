DROP TABLE IF EXISTS `#__gridbox_category_page_map`;
CREATE TABLE `#__gridbox_category_page_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `category_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;