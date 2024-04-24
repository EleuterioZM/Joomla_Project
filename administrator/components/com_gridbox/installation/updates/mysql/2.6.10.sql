ALTER TABLE `#__gridbox_comments` ADD `user_notification` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_comments` ADD `admin_notification` tinyint(1) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS `#__gridbox_comments_unsubscribed_users`;
CREATE TABLE `#__gridbox_comments_unsubscribed_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__gridbox_app` (`title`, `type`) VALUES
('photo-editor', 'system_apps'),
('code-editor', 'system_apps'),
('performance', 'system_apps'),
('preloader', 'system_apps'),
('canonical', 'system_apps'),
('sitemap', 'system_apps');