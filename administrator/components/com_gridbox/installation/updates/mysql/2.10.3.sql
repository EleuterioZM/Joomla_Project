ALTER TABLE `#__gridbox_store_product_data` ADD `product_type` varchar(255);
ALTER TABLE `#__gridbox_store_product_data` ADD `digital_file` text;
ALTER TABLE `#__gridbox_store_order_products` ADD `product_type` varchar(255);
ALTER TABLE `#__gridbox_store_order_products` ADD `product_token` varchar(255);

DROP TABLE IF EXISTS `#__gridbox_store_order_license`;
CREATE TABLE `#__gridbox_store_order_license` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11),
    `order_id` int(11) NOT NULL,
    `downloads` int(11) NOT NULL DEFAULT 0,
    `limit` varchar(255),
    `expires` varchar(255),
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_user_info`;
CREATE TABLE `#__gridbox_store_user_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `customer_id` int(11) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;