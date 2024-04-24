ALTER TABLE `#__gridbox_app` ADD `post_editor_wrapper` text NOT NULL;
ALTER TABLE `#__gridbox_app` ADD `description` text NOT NULL;
ALTER TABLE `#__gridbox_app` ADD `robots` text NOT NULL;
ALTER TABLE `#__gridbox_pages` ADD `robots` text NOT NULL;
ALTER TABLE `#__gridbox_categories` ADD `robots` text NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `robots` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_authors` ADD `robots` varchar(255) NOT NULL DEFAULT '';