DROP TABLE IF EXISTS `#__gridbox_store_bookings`;
CREATE TABLE `#__gridbox_store_bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `start_date` varchar(255) NOT NULL DEFAULT '',
    `end_date` varchar(255) NOT NULL DEFAULT '',
    `start_time` varchar(255) NOT NULL DEFAULT '',
    `end_time` varchar(255) NOT NULL DEFAULT '',
    `guests` varchar(255) NOT NULL DEFAULT '',
    `price` varchar(255) NOT NULL DEFAULT '',
    `later` varchar(255) NOT NULL DEFAULT '',
    `prepaid` varchar(255) NOT NULL DEFAULT '',
    `paid` tinyint(1) NOT NULL DEFAULT 1,
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `reminded` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_bookings_blocks`;
CREATE TABLE `#__gridbox_store_bookings_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `start_date` varchar(255) NOT NULL DEFAULT '',
    `end_date` varchar(255) NOT NULL DEFAULT '',
    `start_time` varchar(255) NOT NULL DEFAULT '',
    `end_time` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__gridbox_api`(`service`, `key`) VALUES
('booking_colors', '{}'),
('booking_calendar', '{}');


ALTER TABLE `#__gridbox_store_orders` ADD `later` varchar(255) NOT NULL DEFAULT "0";

ALTER TABLE `#__gridbox_store_product_data` ADD `booking` text;
ALTER TABLE `#__gridbox_store_cart_products` ADD `booking` text;
ALTER TABLE `#__gridbox_store_wishlist_products` ADD `booking` text;

ALTER TABLE `#__gridbox_pages` CHANGE `title` `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__gridbox_page_fields` CHANGE `value` `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;