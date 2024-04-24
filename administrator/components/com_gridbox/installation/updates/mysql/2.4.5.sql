CREATE TABLE `#__gridbox_instagram` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL,
    `accessToken` varchar(255) NOT NULL,
    `count` int(11) NOT NULL,
    `images` mediumtext NOT NULL,
    `saved_time` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;