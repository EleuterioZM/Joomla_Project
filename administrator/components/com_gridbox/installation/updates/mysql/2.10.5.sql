ALTER TABLE `#__gridbox_store_shipping` ADD `options` text;
ALTER TABLE `#__gridbox_store_product_data` ADD `dimensions` text;
ALTER TABLE `#__gridbox_store_orders_shipping` ADD `type` varchar(255) NOT NULL DEFAULT 'flat';
ALTER TABLE `#__gridbox_store_order_products` CHANGE `product_id` `product_id` INT(11) NOT NULL DEFAULT 0;