ALTER TABLE `#__gridbox_store_wishlist_products` ADD `attachment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_cart_products` ADD `attachment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_product_data` ADD `attachment` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `google_fonts` tinyint(1) NOT NULL DEFAULT 1;

DROP TABLE IF EXISTS `#__gridbox_store_cart_attachments_map`;
CREATE TABLE `#__gridbox_store_cart_attachments_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `wishlist_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `page_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_product_attachments`;
CREATE TABLE `#__gridbox_store_product_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `date` datetime NOT NULL,
    `attachment_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;