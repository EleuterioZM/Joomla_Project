ALTER TABLE `#__gridbox_categories` ADD `schema_markup` text;
ALTER TABLE `#__gridbox_pages` ADD `sitemap_override` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_tags` ADD `sitemap_override` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_authors` ADD `sitemap_override` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_categories` ADD `sitemap_override` int(11) NOT NULL DEFAULT 0;