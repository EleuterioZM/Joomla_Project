ALTER TABLE `#__gridbox_website` ADD `reviews_enable_attachment` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `reviews_attachment_size` int(11) NOT NULL DEFAULT 1024;
ALTER TABLE `#__gridbox_website` ADD `reviews_enable_gravatar` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `reviews_premoderation` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_ip_tracking` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_email_notifications` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `reviews_admin_emails` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `reviews_user_notifications` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `reviews_recaptcha` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `reviews_recaptcha_guests` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_block_links` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_auto_deleting_spam` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_facebook_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_facebook_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `reviews_google_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_google_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `reviews_vk_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `reviews_vk_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `reviews_moderator_label` varchar(255) NOT NULL DEFAULT 'Moderator';
ALTER TABLE `#__gridbox_website` ADD `reviews_moderator_admins` varchar(255) NOT NULL DEFAULT 'super_user';
ALTER TABLE `#__gridbox_website` ADD `sitemap_slash` tinyint(1) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS `#__gridbox_reviews`;
CREATE TABLE `#__gridbox_reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL,
    `parent` int(11) NOT NULL DEFAULT 0,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `status` varchar(255) NOT NULL,
    `ip` varchar(255) NOT NULL,
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `message` text NOT NULL,
    `email` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `avatar` varchar(255) NOT NULL,
    `likes` int(11) NOT NULL DEFAULT 0,
    `dislikes` int(11) NOT NULL DEFAULT 0,
    `user_id` varchar(255) NOT NULL DEFAULT '0',
    `user_type` varchar(255) NOT NULL DEFAULT 'guest',
    `user_notification` tinyint(1) NOT NULL DEFAULT 0,
    `admin_notification` tinyint(1) NOT NULL DEFAULT 0,
    `rating` FLOAT NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_unsubscribed_users`;
CREATE TABLE `#__gridbox_reviews_unsubscribed_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_likes_map`;
CREATE TABLE `#__gridbox_reviews_likes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL,
    `ip` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_attachments`;
CREATE TABLE `#__gridbox_reviews_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_emails`;
CREATE TABLE `#__gridbox_reviews_banned_emails` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_words`;
CREATE TABLE `#__gridbox_reviews_banned_words` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_ip`;
CREATE TABLE `#__gridbox_reviews_banned_ip` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;