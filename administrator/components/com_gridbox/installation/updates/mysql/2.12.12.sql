ALTER TABLE `#__gridbox_store_products_fields` ADD `file_options` text;
ALTER TABLE `#__gridbox_store_cart_attachments_map` ADD `option_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_cart_attachments_map` ADD `order_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_wishlist_products` DROP COLUMN `attachment_id`;
ALTER TABLE `#__gridbox_store_cart_products` DROP COLUMN `attachment_id`;
ALTER TABLE `#__gridbox_store_product_data` DROP COLUMN `attachment`;
DROP TABLE IF EXISTS `#__gridbox_store_order_attachments`;