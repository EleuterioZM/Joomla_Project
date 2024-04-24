ALTER TABLE `#__gridbox_website` ADD `enable_sitemap` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `sitemap_domain` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `sitemap_frequency` varchar(255) NOT NULL DEFAULT 'never';
ALTER TABLE `#__gridbox_website` ADD `adaptive_quality` varchar(255) NOT NULL DEFAULT '60';
ALTER TABLE `#__gridbox_pages` ADD `sitemap_include` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_pages` ADD `changefreq` varchar(255) NOT NULL DEFAULT 'monthly';
ALTER TABLE `#__gridbox_pages` ADD `priority` varchar(255) NOT NULL DEFAULT '0.5';
ALTER TABLE `#__gridbox_app` ADD `sitemap_include` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_app` ADD `changefreq` varchar(255) NOT NULL DEFAULT 'monthly';
ALTER TABLE `#__gridbox_app` ADD `priority` varchar(255) NOT NULL DEFAULT '0.5';
ALTER TABLE `#__gridbox_categories` ADD `sitemap_include` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_categories` ADD `changefreq` varchar(255) NOT NULL DEFAULT 'monthly';
ALTER TABLE `#__gridbox_categories` ADD `priority` varchar(255) NOT NULL DEFAULT '0.5';
ALTER TABLE `#__gridbox_tags` ADD `sitemap_include` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_tags` ADD `changefreq` varchar(255) NOT NULL DEFAULT 'monthly';
ALTER TABLE `#__gridbox_tags` ADD `priority` varchar(255) NOT NULL DEFAULT '0.5';
ALTER TABLE `#__gridbox_authors` ADD `sitemap_include` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_authors` ADD `changefreq` varchar(255) NOT NULL DEFAULT 'monthly';
ALTER TABLE `#__gridbox_authors` ADD `priority` varchar(255) NOT NULL DEFAULT '0.5';

INSERT INTO `#__gridbox_api` (`service`, `key`) VALUES
('gridbox_sitemap', '');