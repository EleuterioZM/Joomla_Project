ALTER TABLE `#__gridbox_store_customer_info` ADD `options` text NOT NULL;
ALTER TABLE `#__gridbox_store_customer_info` ADD `invoice` tinyint(1) NOT NULL DEFAULT 1;
UPDATE `#__gridbox_store_customer_info` SET `options` = '{}';
ALTER TABLE `#__gridbox_store_order_customer_info` ADD `options` text NOT NULL;
ALTER TABLE `#__gridbox_store_order_customer_info` ADD `invoice` tinyint(1) NOT NULL DEFAULT 1;
UPDATE `#__gridbox_store_order_customer_info` SET `options` = '{}';

DROP TABLE IF EXISTS `#__gridbox_store_related_products`;
CREATE TABLE `#__gridbox_store_related_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `related_id` int(11) NOT NULL,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;