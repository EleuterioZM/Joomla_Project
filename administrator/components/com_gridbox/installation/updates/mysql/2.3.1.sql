INSERT INTO `#__gridbox_app` (`title`, `type`, `order_list`) VALUES
('TAGS', 'tags', '50');
ALTER TABLE `#__gridbox_tags` ADD `published` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_tags` ADD `access` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_tags` ADD `language` varchar(255) NOT NULL DEFAULT '*';
ALTER TABLE `#__gridbox_tags` ADD `description` text NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `image` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `meta_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `meta_description` text NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `meta_keywords` text NOT NULL;