DROP TABLE IF EXISTS `#__baforms_api`;
CREATE TABLE `#__baforms_api` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__baforms_api` (`service`, `key`) VALUES
('google_maps', '');