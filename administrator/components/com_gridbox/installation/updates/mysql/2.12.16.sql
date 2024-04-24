DROP TABLE IF EXISTS `#__gridbox_user_avatars`;
CREATE TABLE `#__gridbox_user_avatars` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `avatar` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;