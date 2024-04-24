ALTER TABLE `#__gridbox_app` ADD `page_fonts` text NOT NULL;
ALTER TABLE `#__gridbox_app` ADD `app_fonts` text NOT NULL;
ALTER TABLE `#__gridbox_pages` ADD `saved_time` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_app` ADD `saved_time` varchar(255) NOT NULL DEFAULT '';