ALTER TABLE `#__gridbox_pages` ADD `order_list` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_tags` ADD `order_list` int(11) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS `#__gridbox_filter_state`;
CREATE TABLE `#__gridbox_filter_state` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `value` varchar(255) NOT NULL,
    `user` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;