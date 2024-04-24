DROP TABLE IF EXISTS `#__gridbox_fields_desktop_files`;
CREATE TABLE `#__gridbox_fields_desktop_files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL,
    `app_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;