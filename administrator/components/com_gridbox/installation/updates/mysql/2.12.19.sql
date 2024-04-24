DROP TABLE IF EXISTS `#__gridbox_email_delay`;
CREATE TABLE `#__gridbox_email_delay` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `status` tinyint(1) NOT NULL DEFAULT 0,
    `order_id` int(11) NOT NULL,
    `notification` varchar(255) NOT NULL DEFAULT "",
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;