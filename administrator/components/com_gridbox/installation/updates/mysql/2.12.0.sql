ALTER TABLE `#__gridbox_store_product_data` ADD `subscription` text;
ALTER TABLE `#__gridbox_store_cart_products` ADD `renew_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_cart_products` ADD `plan_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_cart_products` ADD `upgrade_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_order_products` ADD `renew_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_order_products` ADD `plan_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_order_products` ADD `upgrade_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_order_products` ADD `upgrade_price` varchar(255) NOT NULL DEFAULT "0";

DROP TABLE IF EXISTS `#__gridbox_store_subscriptions`;
CREATE TABLE `#__gridbox_store_subscriptions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `action` varchar(255) NOT NULL DEFAULT "",
    `user_groups` varchar(255) NOT NULL DEFAULT "",
    `date` varchar(255) NOT NULL DEFAULT "",
    `expires` varchar(255) NOT NULL DEFAULT "",
    `reminded` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_subscriptions_map`;
CREATE TABLE `#__gridbox_store_subscriptions_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `last_status` varchar(255) NOT NULL DEFAULT "completed",
    `start_date` varchar(255) NOT NULL DEFAULT "",
    `expires` varchar(255) NOT NULL DEFAULT "",
    `product_id` int(11) NOT NULL DEFAULT 0,
    `subscription_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;