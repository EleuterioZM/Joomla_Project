INSERT INTO `#__gridbox_system_pages`(`title`, `type`, `theme`, `order_list`, `page_options`) VALUES
('Preloader', 'preloader', 0, 1, '{}');

INSERT INTO `#__gridbox_api` (`service`, `key`) VALUES
('openweathermap', ''),
('yandex_maps', '');

DROP TABLE IF EXISTS `#__gridbox_weather`;
CREATE TABLE `#__gridbox_weather` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL,
    `location` varchar(255) NOT NULL,
    `data` mediumtext NOT NULL,
    `saved_time` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;