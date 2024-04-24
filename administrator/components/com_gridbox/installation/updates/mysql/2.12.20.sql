ALTER TABLE `#__gridbox_pages` ADD `schema_markup` text;
ALTER TABLE `#__gridbox_app` ADD `schema_markup` text;
ALTER TABLE `#__gridbox_tags` ADD `schema_markup` text;
ALTER TABLE `#__gridbox_authors` ADD `schema_markup` text;

DROP TABLE IF EXISTS `#__gridbox_seo_defaults`;
CREATE TABLE `#__gridbox_seo_defaults` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11)  NOT NULL DEFAULT 0,
    `item_type` varchar(255) NOT NULL DEFAULT "page",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `share_image` varchar(255) NOT NULL DEFAULT '',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_include` varchar(255) NOT NULL DEFAULT '',
    `changefreq` varchar(255) NOT NULL DEFAULT '',
    `priority` varchar(255) NOT NULL DEFAULT '',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;