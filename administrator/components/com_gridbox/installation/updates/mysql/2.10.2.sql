ALTER TABLE `#__gridbox_store_cart_products` ADD `extra_options` text;
ALTER TABLE `#__gridbox_store_wishlist_products` ADD `extra_options` text;
ALTER TABLE `#__gridbox_store_order_products` ADD `extra_options` text;
ALTER TABLE `#__gridbox_store_products_fields` ADD `required` tinyint(1) NOT NULL DEFAULT 0;