ALTER TABLE `#__gridbox_app` ADD `fields_groups` text NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `currency_symbol` varchar(255) NOT NULL DEFAULT "$";
ALTER TABLE `#__gridbox_website` ADD `currency_code` varchar(255) NOT NULL DEFAULT "USD";
ALTER TABLE `#__gridbox_website` ADD `currency_position` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_website` ADD `thousand_separator` varchar(255) NOT NULL DEFAULT ",";
ALTER TABLE `#__gridbox_website` ADD `decimal_separator` varchar(255) NOT NULL DEFAULT ".";
ALTER TABLE `#__gridbox_website` ADD `decimals_number` int(11) NOT NULL DEFAULT 2;
ALTER TABLE `#__gridbox_website` ADD `enable_canonical` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `canonical_domain` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `image_path` varchar(255) NOT NULL DEFAULT "images";
ALTER TABLE `#__gridbox_website` ADD `file_types` varchar(255) NOT NULL DEFAULT "csv, doc, gif, ico, jpg, jpeg, pdf, png, txt, xls, svg, mp4, webp";
ALTER TABLE `#__gridbox_website` ADD `email_encryption` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `enable_attachment` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `attachment_size` int(11) NOT NULL DEFAULT 1024;
ALTER TABLE `#__gridbox_website` ADD `attachment_types` varchar(255) NOT NULL DEFAULT 'csv, doc, gif, ico, jpg, jpeg, pdf, png, txt, xls, svg, mp4, webp';
ALTER TABLE `#__gridbox_website` ADD `enable_gravatar` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `comments_premoderation` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `ip_tracking` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `email_notifications` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `admin_emails` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `user_notifications` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_website` ADD `comments_recaptcha` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `comments_recaptcha_guests` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_block_links` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_auto_deleting_spam` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_facebook_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_facebook_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `comments_google_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_google_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `comments_vk_login` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_website` ADD `comments_vk_login_key` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_website` ADD `comments_moderator_label` varchar(255) NOT NULL DEFAULT 'Moderator';
ALTER TABLE `#__gridbox_website` ADD `comments_moderator_admins` varchar(255) NOT NULL DEFAULT 'super_user';
ALTER TABLE `#__gridbox_pages` ADD `share_image` varchar(255) NOT NULL DEFAULT 'share_image';
ALTER TABLE `#__gridbox_pages` ADD `share_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_pages` ADD `share_description` text NOT NULL;
ALTER TABLE `#__gridbox_app` ADD `share_image` varchar(255) NOT NULL DEFAULT 'share_image';
ALTER TABLE `#__gridbox_app` ADD `share_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_app` ADD `share_description` text NOT NULL;
ALTER TABLE `#__gridbox_categories` ADD `share_image` varchar(255) NOT NULL DEFAULT 'share_image';
ALTER TABLE `#__gridbox_categories` ADD `share_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_categories` ADD `share_description` text NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `share_image` varchar(255) NOT NULL DEFAULT 'share_image';
ALTER TABLE `#__gridbox_tags` ADD `share_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_tags` ADD `share_description` text NOT NULL;
ALTER TABLE `#__gridbox_authors` ADD `share_image` varchar(255) NOT NULL DEFAULT 'share_image';
ALTER TABLE `#__gridbox_authors` ADD `share_title` varchar(255) NOT NULL;
ALTER TABLE `#__gridbox_authors` ADD `share_description` text NOT NULL;

DROP TABLE IF EXISTS `#__gridbox_comments`;
CREATE TABLE `#__gridbox_comments` (
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
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_comments_likes_map`;
CREATE TABLE `#__gridbox_comments_likes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL,
    `ip` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_comments_attachments`;
CREATE TABLE `#__gridbox_comments_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_emails`;
CREATE TABLE `#__gridbox_comments_banned_emails` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_words`;
CREATE TABLE `#__gridbox_comments_banned_words` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_ip`;
CREATE TABLE `#__gridbox_comments_banned_ip` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;