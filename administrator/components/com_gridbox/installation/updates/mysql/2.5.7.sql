ALTER TABLE `#__gridbox_pages` ADD `featured` int(11) NOT NULL DEFAULT 0;
INSERT INTO `#__gridbox_system_pages`(`title`, `type`, `theme`, `order_list`, `page_options`) VALUES
('Search Results Page', 'search', 0, 1, '{}');