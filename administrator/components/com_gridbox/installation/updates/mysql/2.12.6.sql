DROP TABLE IF EXISTS `#__gridbox_apps_groups`;
CREATE TABLE `#__gridbox_apps_groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_apps_order_map`;
CREATE TABLE `#__gridbox_apps_order_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11) NOT NULL,
    `parent_id` int(11) NOT NULL DEFAULT 0,
    `order_ind` int(11) NOT NULL DEFAULT 0,
    `type` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;