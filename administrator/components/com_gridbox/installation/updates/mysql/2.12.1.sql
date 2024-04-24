ALTER TABLE `#__gridbox_website` ADD `google_analytics` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `google_analytics_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_website` ADD `google_gtm_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_website` ADD `yandex_metrica` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `yandex_tag_number` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_website` ADD `ecommerce_tracking` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `facebook_pixel` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `facebook_pixel_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_store_products_fields` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";

DROP TABLE IF EXISTS `#__gridbox_store_sales`;
CREATE TABLE `#__gridbox_store_sales` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `applies_to` varchar(255) NOT NULL DEFAULT '*',
    `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_store_sales_map`;
CREATE TABLE `#__gridbox_store_sales_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(255) NOT NULL DEFAULT "",
    `sale_id` int(11) NOT NULL DEFAULT 0,
    `item_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;