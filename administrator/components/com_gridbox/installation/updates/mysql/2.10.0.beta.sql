DROP TABLE IF EXISTS `#__gridbox_store_payment_methods`;
CREATE TABLE `#__gridbox_store_payment_methods` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `settings` text NOT NULL,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_promo_codes`;
CREATE TABLE `#__gridbox_store_promo_codes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `code` varchar(255) NOT NULL DEFAULT '',
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `applies_to` varchar(255) NOT NULL DEFAULT '*',
    `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `limit` int(11) NOT NULL DEFAULT 0,
    `used` int(11) NOT NULL DEFAULT 0,
    `disable_sales` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_promo_codes_map`;
CREATE TABLE `#__gridbox_store_promo_codes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(255) NOT NULL,
    `code_id` int(11) NOT NULL,
    `item_id` int(11) NOT NULL,
    `variation` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_shipping`;
CREATE TABLE `#__gridbox_store_shipping` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `price` varchar(255) NOT NULL DEFAULT '',
    `free` varchar(255) NOT NULL DEFAULT '',
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_products_fields`;
CREATE TABLE `#__gridbox_store_products_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_key` varchar(255) NOT NULL,
    `field_type` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `options` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_products_fields_data`;
CREATE TABLE `#__gridbox_store_products_fields_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_id` int(11) NOT NULL,
    `option_key` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    `color` varchar(255) NOT NULL DEFAULT '',
    `image` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_product_data`;
CREATE TABLE `#__gridbox_store_product_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `price` varchar(255) NOT NULL,
    `sale_price` varchar(255) NOT NULL,
    `sku` varchar(255) NOT NULL,
    `stock` varchar(255) NOT NULL,
    `variations` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_product_variations_map`;
CREATE TABLE `#__gridbox_store_product_variations_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `option_key` varchar(255) NOT NULL,
    `images` text NOT NULL,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `order_group` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_customer_info`;
CREATE TABLE `#__gridbox_store_customer_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `required` tinyint(1) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_cart`;
CREATE TABLE `#__gridbox_store_cart` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `promo_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_cart_products`;
CREATE TABLE `#__gridbox_store_cart_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cart_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `variation` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_orders_status_history`;
CREATE TABLE `#__gridbox_store_orders_status_history` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `date` datetime NOT NULL,
    `status` varchar(255) NOT NULL DEFAULT 'new',
    `comment` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_orders`;
CREATE TABLE `#__gridbox_store_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` datetime NOT NULL,
    `cart_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `status` varchar(255) NOT NULL DEFAULT 'new',
    `published` tinyint(1) NOT NULL DEFAULT 0,
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `order_number` varchar(255) NOT NULL DEFAULT '#00000000',
    `subtotal` varchar(255) NOT NULL,
    `tax` varchar(255) NOT NULL,
    `total` varchar(255) NOT NULL,
    `currency_symbol` varchar(255) NOT NULL,
    `currency_position` varchar(255) NOT NULL,
    `params` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_orders_discount`;
CREATE TABLE `#__gridbox_store_orders_discount` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `promo_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `code` varchar(255) NOT NULL,
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `value` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_orders_shipping`;
CREATE TABLE `#__gridbox_store_orders_shipping` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `shipping_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `price` varchar(255) NOT NULL,
    `tax` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_orders_payment`;
CREATE TABLE `#__gridbox_store_orders_payment` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `payment_id` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_order_customer_info`;
CREATE TABLE `#__gridbox_store_order_customer_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `customer_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_order_products`;
CREATE TABLE `#__gridbox_store_order_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` varchar(255) NOT NULL,
    `sale_price` varchar(255) NOT NULL,
    `sku` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_order_product_variations`;
CREATE TABLE `#__gridbox_store_order_product_variations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `type` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    `color` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_wishlist`;
CREATE TABLE `#__gridbox_store_wishlist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_wishlist_products`;
CREATE TABLE `#__gridbox_store_wishlist_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `wishlist_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `variation` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_badges`;
CREATE TABLE `#__gridbox_store_badges` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `color` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_badges_map`;
CREATE TABLE `#__gridbox_store_badges_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `badge_id` int(11) NOT NULL,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__gridbox_store_badges` (`title`, `color`, `type`) VALUES
('', '#f64231', 'sale'),
('Sale', '#ff7a2f', ''),
('New', '#34dca2', ''),
('Hot', '#ffc700', '');

INSERT INTO `#__gridbox_api` (`service`, `key`) VALUES
('store', '{}');

ALTER TABLE `#__gridbox_system_pages` ADD `alias` varchar(255) NOT NULL DEFAULT '';

UPDATE `#__gridbox_system_pages` SET `alias` = 'search' WHERE `type` = 'search';

INSERT INTO `#__gridbox_system_pages` (`title`, `alias`, `type`, `theme`, `order_list`, `page_options`, `html`, `items`, `fonts`) VALUES
('Checkout Page', 'checkout', 'checkout', 0, 1, '{}', '', '', ''),
('Thank You Page', 'thank-you', 'thank-you-page', 0, 1, '{}', '', '', ''),
('Store Search Results Page', 'store-search', 'store-search', 0, 1, '{}', '', '', '');

INSERT INTO `#__gridbox_store_customer_info` (`title`, `type`, `required`, `order_list`) VALUES
('Name', 'text', 1, 0),
('Phone', 'text', 0, 1),
('Email', 'email', 1, 2),
('Country', 'text', 0, 3),
('State / Province', 'text', 0, 4),
('Street Address', 'text', 0, 5),
('City', 'text', 0, 6),
('Zip Code', 'text', 0, 7),
('Comments', 'textarea', 0, 8);