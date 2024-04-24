DROP TABLE IF EXISTS `#__gridbox_page_fields`;
CREATE TABLE `#__gridbox_page_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL,
    `field_id` varchar(255) NOT NULL,
    `field_type` varchar(255) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_fields`;
CREATE TABLE `#__gridbox_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `app_id` int(11) NOT NULL,
    `field_key` varchar(255) NOT NULL,
    `field_type` varchar(255) NOT NULL,
    `label` varchar(255) NOT NULL,
    `required` tinyint(1) NOT NULL,
    `options` text NOT NULL,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_fields_data`;
CREATE TABLE `#__gridbox_fields_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_id` int(11) NOT NULL,
    `field_type` varchar(255) NOT NULL,
    `option_key` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;