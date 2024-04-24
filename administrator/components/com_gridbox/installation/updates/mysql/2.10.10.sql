ALTER TABLE `#__gridbox_website` ADD `upload_compress_images` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `upload_images_size` varchar(255) NOT NULL DEFAULT '1440';
ALTER TABLE `#__gridbox_website` ADD `upload_images_quality` varchar(255) NOT NULL DEFAULT '60';
ALTER TABLE `#__gridbox_website` ADD `upload_images_webp` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_page_blocks` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_page_blocks` CHANGE `item` `item` mediumtext;
ALTER TABLE `#__gridbox_page_blocks` CHANGE `image` `image` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_page_blocks` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings` CHANGE `plugin_id` `plugin_id` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings` CHANGE `option` `option` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings` CHANGE `view` `view` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings` CHANGE `page_id` `page_id` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings` CHANGE `rating` `rating` FLOAT;
ALTER TABLE `#__gridbox_star_ratings` CHANGE `count` `count` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_star_ratings_users` CHANGE `plugin_id` `plugin_id` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings_users` CHANGE `option` `option` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings_users` CHANGE `view` `view` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings_users` CHANGE `page_id` `page_id` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_star_ratings_users` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_page_fields` CHANGE `page_id` `page_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_page_fields` CHANGE `field_id` `field_id` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_page_fields` CHANGE `field_type` `field_type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_page_fields` CHANGE `value` `value` text;
ALTER TABLE `#__gridbox_fields` CHANGE `app_id` `app_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_fields` CHANGE `field_key` `field_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields` CHANGE `field_type` `field_type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields` CHANGE `label` `label` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields` CHANGE `required` `required` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_fields` CHANGE `options` `options` text;
ALTER TABLE `#__gridbox_fields_data` CHANGE `field_id` `field_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_fields_data` CHANGE `field_type` `field_type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields_data` CHANGE `option_key` `option_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields_data` CHANGE `value` `value` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields_desktop_files` CHANGE `page_id` `page_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_fields_desktop_files` CHANGE `app_id` `app_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_fields_desktop_files` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_fields_desktop_files` CHANGE `filename` `filename` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_products_fields` CHANGE `field_key` `field_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_products_fields` CHANGE `field_type` `field_type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_products_fields` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_products_fields` CHANGE `options` `options` text;
ALTER TABLE `#__gridbox_store_products_fields_data` CHANGE `field_id` `field_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_products_fields_data` CHANGE `option_key` `option_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_products_fields_data` CHANGE `value` `value` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_product_data` CHANGE `product_id` `product_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_product_data` CHANGE `price` `price` varchar(255) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_product_data` CHANGE `sale_price` `sale_price` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_product_data` CHANGE `sku` `sku` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_product_data` CHANGE `stock` `stock` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_product_data` CHANGE `variations` `variations` text;
ALTER TABLE `#__gridbox_store_product_data` CHANGE `extra_options` `extra_options` text;
ALTER TABLE `#__gridbox_store_product_variations_map` CHANGE `product_id` `product_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_product_variations_map` CHANGE `field_id` `field_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_product_variations_map` CHANGE `option_key` `option_key` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_product_variations_map` CHANGE `images` `images` text;