DROP TABLE IF EXISTS `#__gridbox_store_order_tracking`;
CREATE TABLE `#__gridbox_store_order_tracking` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `number` text,
    `url` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;