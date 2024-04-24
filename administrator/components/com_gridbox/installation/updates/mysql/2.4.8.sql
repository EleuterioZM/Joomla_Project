DROP TABLE IF EXISTS `#__gridbox_system_pages`;
CREATE TABLE `#__gridbox_system_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `theme` varchar(255) NOT NULL,
    `html` mediumtext NOT NULL,
    `items` mediumtext NOT NULL,
    `fonts` text NOT NULL,
    `saved_time` varchar(255) NOT NULL DEFAULT '',
    `order_list` int(11) NOT NULL DEFAULT 0,
    `page_options` mediumtext NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__gridbox_system_pages`(`title`, `type`, `theme`, `order_list`, `page_options`) VALUES
('404 Error Page', '404', 0, 1, '{"enable_header":false}');

