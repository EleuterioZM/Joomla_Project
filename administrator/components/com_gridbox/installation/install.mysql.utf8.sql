ALTER TABLE `#__template_styles` CHANGE `params` `params` MEDIUMTEXT;

DROP TABLE IF EXISTS `#__gridbox_pages`;
CREATE TABLE `#__gridbox_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `theme` varchar(255) NOT NULL DEFAULT "",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `meta_keywords` text,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `params` mediumtext,
    `style` mediumtext,
    `fonts` text,
    `intro_image` varchar(255) NOT NULL DEFAULT "",
    `page_alias` varchar(255) NOT NULL DEFAULT "",
    `page_category` varchar(255) NOT NULL DEFAULT "",
    `page_access` int(11) NOT NULL DEFAULT 1,
    `intro_text` mediumtext,
    `image_alt` varchar(255) NOT NULL DEFAULT "",
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `end_publishing` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `hits` int(11) NOT NULL DEFAULT 0,
    `language` varchar(255) NOT NULL DEFAULT '*',
    `app_id` int(11) NOT NULL DEFAULT 0,
    `saved_time` varchar(255) NOT NULL DEFAULT '',
    `class_suffix` varchar(255) NOT NULL DEFAULT '',
    `order_list` int(11) NOT NULL DEFAULT 0,
    `featured` int(11) NOT NULL DEFAULT 0,
    `root_order_list` int(11) NOT NULL DEFAULT 0,
    `robots` varchar(255) NOT NULL DEFAULT '',
    `share_image` varchar(255) NOT NULL DEFAULT 'share_image',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_override` int(11) NOT NULL DEFAULT 0,
    `sitemap_include` int(11) NOT NULL DEFAULT 1,
    `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
    `priority` varchar(255) NOT NULL DEFAULT '0.5',
    `schema_markup` text,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `email_sended` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_app`;
CREATE TABLE `#__gridbox_app` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `alias` varchar(255) NOT NULL DEFAULT "",
    `theme` int(11) NOT NULL DEFAULT 0,
    `type` varchar(255) NOT NULL DEFAULT "",
    `page_layout` mediumtext,
    `page_items` mediumtext,
    `page_fonts` text,
    `app_fonts` text,
    `app_layout` mediumtext,
    `app_items` mediumtext,
    `saved_time` varchar(255) NOT NULL DEFAULT '',
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `access` tinyint(1) NOT NULL DEFAULT 1,
    `language` varchar(255) NOT NULL DEFAULT '*',
    `image` varchar(255) NOT NULL DEFAULT "",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `meta_keywords` text,
    `order_list` int(11) NOT NULL DEFAULT 1,
    `post_editor_wrapper` text,
    `description` text,
    `robots` varchar(255) NOT NULL DEFAULT '',
    `fields_groups` text,
    `share_image` varchar(255) NOT NULL DEFAULT 'share_image',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_include` int(11) NOT NULL DEFAULT 1,
    `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
    `priority` varchar(255) NOT NULL DEFAULT '0.5',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_apps_groups`;
CREATE TABLE `#__gridbox_apps_groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_apps_order_map`;
CREATE TABLE `#__gridbox_apps_order_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11) NOT NULL,
    `parent_id` int(11) NOT NULL DEFAULT 0,
    `order_ind` int(11) NOT NULL DEFAULT 0,
    `type` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_associations`;
CREATE TABLE `#__gridbox_associations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11),
    `item_type` varchar(255) NOT NULL DEFAULT "",
    `hash` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_fonts`;
CREATE TABLE `#__gridbox_fonts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `font` varchar(255) NOT NULL DEFAULT "",
    `styles` varchar(255) NOT NULL DEFAULT "",
    `custom_src` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_plugins`;
CREATE TABLE `#__gridbox_plugins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `image` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `joomla_constant` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_page_blocks`;
CREATE TABLE `#__gridbox_page_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `item` mediumtext,
    `image` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_library`;
CREATE TABLE `#__gridbox_library` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `item` mediumtext,
    `type` varchar(255) NOT NULL DEFAULT 'section',
    `global_item` varchar(255) NOT NULL DEFAULT "",
    `image` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_categories`;
CREATE TABLE `#__gridbox_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `alias` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `access` tinyint(1) NOT NULL DEFAULT 1,
    `app_id` int(11) NOT NULL DEFAULT 0,
    `language` varchar(255) NOT NULL DEFAULT '*',
    `description` text,
    `image` varchar(255) NOT NULL DEFAULT "",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `meta_keywords` text,
    `parent` int(11) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 1,
    `robots` varchar(255) NOT NULL DEFAULT '',
    `share_image` varchar(255) NOT NULL DEFAULT 'share_image',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_override` int(11) NOT NULL DEFAULT 0,
    `sitemap_include` int(11) NOT NULL DEFAULT 1,
    `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
    `priority` varchar(255) NOT NULL DEFAULT '0.5',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_tags`;
CREATE TABLE `#__gridbox_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `alias` varchar(255) NOT NULL DEFAULT "",
    `hits` int(11) NOT NULL DEFAULT 0,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `access` tinyint(1) NOT NULL DEFAULT 1,
    `language` varchar(255) NOT NULL DEFAULT '*',
    `description` text,
    `image` varchar(255) NOT NULL DEFAULT "",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `meta_keywords` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `robots` varchar(255) NOT NULL DEFAULT '',
    `share_image` varchar(255) NOT NULL DEFAULT 'share_image',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_override` int(11) NOT NULL DEFAULT 0,
    `sitemap_include` int(11) NOT NULL DEFAULT 1,
    `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
    `priority` varchar(255) NOT NULL DEFAULT '0.5',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_authors`;
CREATE TABLE `#__gridbox_authors` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `alias` varchar(255) NOT NULL DEFAULT "",
    `hits` int(11) NOT NULL DEFAULT 0,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `avatar` varchar(255) NOT NULL DEFAULT '',
    `description` text,
    `image` varchar(255) NOT NULL DEFAULT "",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `meta_keywords` text,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `author_social` text,
    `robots` varchar(255) NOT NULL DEFAULT '',
    `share_image` varchar(255) NOT NULL DEFAULT 'share_image',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_override` int(11) NOT NULL DEFAULT 0,
    `sitemap_include` int(11) NOT NULL DEFAULT 1,
    `changefreq` varchar(255) NOT NULL DEFAULT 'monthly',
    `priority` varchar(255) NOT NULL DEFAULT '0.5',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_authors_map`;
CREATE TABLE `#__gridbox_authors_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `author_id` int(11) NOT NULL DEFAULT 0,
    `page_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_tags_map`;
CREATE TABLE `#__gridbox_tags_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) NOT NULL DEFAULT 0,
    `page_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_api`;
CREATE TABLE `#__gridbox_api` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `service` varchar(255) NOT NULL DEFAULT "",
    `key` mediumtext,
    `type` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_website`;
CREATE TABLE `#__gridbox_website` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `favicon` varchar(255) NOT NULL DEFAULT "",
    `header_code` mediumtext,
    `body_code` mediumtext,
    `enable_autosave` varchar(255) NOT NULL DEFAULT "false",
    `autosave_delay` varchar(255) NOT NULL DEFAULT "10",
    `breakpoints` text,
    `date_format` varchar(255) NOT NULL DEFAULT "j F Y",
    `container` varchar(255) NOT NULL DEFAULT '1170',
    `disable_responsive` tinyint(1) NOT NULL DEFAULT 0,
    `compress_html` tinyint(1) NOT NULL DEFAULT 0,
    `compress_css` tinyint(1) NOT NULL DEFAULT 0,
    `compress_js` tinyint(1) NOT NULL DEFAULT 0,
    `compress_images` tinyint(1) NOT NULL DEFAULT 0,
    `images_max_size` varchar(255) NOT NULL DEFAULT '1440',
    `images_quality` varchar(255) NOT NULL DEFAULT '60',
    `compress_images_webp` tinyint(1) NOT NULL DEFAULT 0,
    `page_cache` tinyint(1) NOT NULL DEFAULT 0,
    `browser_cache` tinyint(1) NOT NULL DEFAULT 0,
    `images_lazy_load` tinyint(1) NOT NULL DEFAULT 0,
    `adaptive_images` tinyint(1) NOT NULL DEFAULT 0,
    `adaptive_quality` varchar(255) NOT NULL DEFAULT '60',
    `adaptive_images_webp` tinyint(1) NOT NULL DEFAULT 0,
    `preloader` tinyint(1) NOT NULL DEFAULT 0,
    `currency_code` varchar(255) NOT NULL DEFAULT "USD",
    `enable_canonical` tinyint(1) NOT NULL DEFAULT 0,
    `canonical_domain` varchar(255) NOT NULL DEFAULT "",
    `enable_sitemap` tinyint(1) NOT NULL DEFAULT 0,
    `sitemap_domain` varchar(255) NOT NULL DEFAULT "",
    `sitemap_frequency` varchar(255) NOT NULL DEFAULT 'never',
    `image_path` varchar(255) NOT NULL DEFAULT 'images',
    `file_types` varchar(255) NOT NULL DEFAULT 'csv, doc, gif, ico, jpg, jpeg, pdf, png, txt, xls, svg, mp4, webp',
    `email_encryption` tinyint(1) NOT NULL DEFAULT 0,
    `enable_attachment` tinyint(1) NOT NULL DEFAULT 1,
    `attachment_size` int(11) NOT NULL DEFAULT 1024,
    `attachment_types` varchar(255) NOT NULL DEFAULT 'csv, doc, gif, ico, jpg, jpeg, pdf, png, txt, xls, svg, mp4, webp',
    `enable_gravatar` tinyint(1) NOT NULL DEFAULT 1,
    `comments_premoderation` tinyint(1) NOT NULL DEFAULT 0,
    `ip_tracking` tinyint(1) NOT NULL DEFAULT 0,
    `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `author_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `user_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `comments_recaptcha` varchar(255) NOT NULL DEFAULT "",
    `comments_recaptcha_guests` tinyint(1) NOT NULL DEFAULT 0,
    `comments_block_links` tinyint(1) NOT NULL DEFAULT 0,
    `comments_auto_deleting_spam` tinyint(1) NOT NULL DEFAULT 0,
    `comments_facebook_login` tinyint(1) NOT NULL DEFAULT 0,
    `comments_facebook_login_key` varchar(255) NOT NULL DEFAULT "",
    `comments_google_login` tinyint(1) NOT NULL DEFAULT 0,
    `comments_google_login_key` varchar(255) NOT NULL DEFAULT "",
    `comments_vk_login` tinyint(1) NOT NULL DEFAULT 0,
    `comments_vk_login_key` varchar(255) NOT NULL DEFAULT "",
    `comments_moderator_label` varchar(255) NOT NULL DEFAULT 'Moderator',
    `comments_moderator_admins` varchar(255) NOT NULL DEFAULT 'super_user',
    `reviews_enable_attachment` tinyint(1) NOT NULL DEFAULT 1,
    `reviews_attachment_size` int(11) NOT NULL DEFAULT 1024,
    `reviews_enable_gravatar` tinyint(1) NOT NULL DEFAULT 1,
    `reviews_premoderation` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_ip_tracking` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_email_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `reviews_admin_emails` varchar(255) NOT NULL DEFAULT "",
    `reviews_author_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `reviews_user_notifications` tinyint(1) NOT NULL DEFAULT 1,
    `reviews_recaptcha` varchar(255) NOT NULL DEFAULT "",
    `reviews_recaptcha_guests` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_block_links` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_auto_deleting_spam` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_facebook_login` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_facebook_login_key` varchar(255) NOT NULL DEFAULT "",
    `reviews_google_login` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_google_login_key` varchar(255) NOT NULL DEFAULT "",
    `reviews_vk_login` tinyint(1) NOT NULL DEFAULT 0,
    `reviews_vk_login_key` varchar(255) NOT NULL DEFAULT "",
    `reviews_moderator_label` varchar(255) NOT NULL DEFAULT 'Moderator',
    `reviews_moderator_admins` varchar(255) NOT NULL DEFAULT 'super_user',
    `sitemap_slash` tinyint(1) NOT NULL DEFAULT 0,
    `canonical_slash` tinyint(1) NOT NULL DEFAULT 0,
    `defer_loading` tinyint(1) NOT NULL DEFAULT 0,
    `upload_compress_images` tinyint(1) NOT NULL DEFAULT 0,
    `upload_images_size` varchar(255) NOT NULL DEFAULT '1440',
    `upload_images_quality` varchar(255) NOT NULL DEFAULT '60',
    `upload_images_webp` tinyint(1) NOT NULL DEFAULT 0,
    `google_analytics` tinyint(1) NOT NULL DEFAULT 0,
    `google_analytics_id` varchar(255) NOT NULL DEFAULT '',
    `google_gtm_id` varchar(255) NOT NULL DEFAULT '',
    `ecommerce_tracking` tinyint(1) NOT NULL DEFAULT 0,
    `yandex_metrica` tinyint(1) NOT NULL DEFAULT 0,
    `yandex_tag_number` varchar(255) NOT NULL DEFAULT '',
    `facebook_pixel` tinyint(1) NOT NULL DEFAULT 0,
    `facebook_pixel_id` varchar(255) NOT NULL DEFAULT '',
    `google_fonts` tinyint(1) NOT NULL DEFAULT 1,
    `versions_auto_save` tinyint(1) NOT NULL DEFAULT 0,
    `max_versions` varchar(255) NOT NULL DEFAULT '10',
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_star_ratings`;
CREATE TABLE IF NOT EXISTS  `#__gridbox_star_ratings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL DEFAULT "",
    `option` varchar(255) NOT NULL DEFAULT "",
    `view` varchar(255) NOT NULL DEFAULT "",
    `page_id` varchar(255) NOT NULL DEFAULT "",
    `rating` FLOAT,
    `count` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_star_ratings_users`;
CREATE TABLE IF NOT EXISTS  `#__gridbox_star_ratings_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL DEFAULT "",
    `option` varchar(255) NOT NULL DEFAULT "",
    `view` varchar(255) NOT NULL DEFAULT "",
    `page_id` varchar(255) NOT NULL DEFAULT "",
    `ip` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_filter_state`;
CREATE TABLE `#__gridbox_filter_state` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT "",
    `value` varchar(255) NOT NULL DEFAULT "",
    `user` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_instagram`;
CREATE TABLE `#__gridbox_instagram` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL,
    `accessToken` varchar(255) NOT NULL,
    `count` int(11) NOT NULL,
    `images` mediumtext NOT NULL,
    `saved_time` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_weather`;
CREATE TABLE `#__gridbox_weather` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin_id` varchar(255) NOT NULL DEFAULT "",
    `location` varchar(255) NOT NULL DEFAULT "",
    `data` mediumtext,
    `saved_time` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_custom_user_icons`;
CREATE TABLE `#__gridbox_custom_user_icons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `group` varchar(255) NOT NULL DEFAULT "",
    `path` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_system_pages`;
CREATE TABLE `#__gridbox_system_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `alias` varchar(255) NOT NULL DEFAULT '',
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `type` varchar(255) NOT NULL DEFAULT "",
    `theme` varchar(255) NOT NULL DEFAULT "",
    `language` varchar(255) NOT NULL DEFAULT "*",
    `html` mediumtext,
    `items` mediumtext,
    `fonts` text,
    `saved_time` varchar(255) NOT NULL DEFAULT '',
    `order_list` int(11) NOT NULL DEFAULT 0,
    `page_options` mediumtext,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_page_fields`;
CREATE TABLE `#__gridbox_page_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `field_id` varchar(255) NOT NULL DEFAULT "",
    `field_type` varchar(255) NOT NULL DEFAULT "",
    `value` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_fields`;
CREATE TABLE `#__gridbox_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `app_id` int(11) NOT NULL DEFAULT 0,
    `field_key` varchar(255) NOT NULL DEFAULT "",
    `field_type` varchar(255) NOT NULL DEFAULT "",
    `label` varchar(255) NOT NULL DEFAULT "",
    `required` tinyint(1) NOT NULL DEFAULT 0,
    `options` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_fields_data`;
CREATE TABLE `#__gridbox_fields_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_id` int(11) NOT NULL DEFAULT 0,
    `field_type` varchar(255) NOT NULL DEFAULT "",
    `option_key` varchar(255) NOT NULL DEFAULT "",
    `value` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments`;
CREATE TABLE `#__gridbox_comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `parent` int(11) NOT NULL DEFAULT 0,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `status` varchar(255) NOT NULL DEFAULT "",
    `ip` varchar(255) NOT NULL DEFAULT "",
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `message` text,
    `email` varchar(255) NOT NULL DEFAULT "",
    `name` varchar(255) NOT NULL DEFAULT "",
    `avatar` varchar(255) NOT NULL DEFAULT "",
    `likes` int(11) NOT NULL DEFAULT 0,
    `dislikes` int(11) NOT NULL DEFAULT 0,
    `user_id` varchar(255) NOT NULL DEFAULT '0',
    `user_type` varchar(255) NOT NULL DEFAULT 'guest',
    `user_notification` tinyint(1) NOT NULL DEFAULT 0,
    `admin_notification` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_unsubscribed_users`;
CREATE TABLE `#__gridbox_comments_unsubscribed_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_likes_map`;
CREATE TABLE `#__gridbox_comments_likes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL DEFAULT 0,
    `ip` varchar(255) NOT NULL DEFAULT "",
    `status` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_attachments`;
CREATE TABLE `#__gridbox_comments_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL DEFAULT 0,
    `name` varchar(255) NOT NULL DEFAULT "",
    `filename` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_emails`;
CREATE TABLE `#__gridbox_comments_banned_emails` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_words`;
CREATE TABLE `#__gridbox_comments_banned_words` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_comments_banned_ip`;
CREATE TABLE `#__gridbox_comments_banned_ip` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews`;
CREATE TABLE `#__gridbox_reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `parent` int(11) NOT NULL DEFAULT 0,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `status` varchar(255) NOT NULL DEFAULT "",
    `ip` varchar(255) NOT NULL DEFAULT "",
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `message` text,
    `email` varchar(255) NOT NULL DEFAULT "",
    `name` varchar(255) NOT NULL DEFAULT "",
    `avatar` varchar(255) NOT NULL DEFAULT "",
    `likes` int(11) NOT NULL DEFAULT 0,
    `dislikes` int(11) NOT NULL DEFAULT 0,
    `user_id` varchar(255) NOT NULL DEFAULT '0',
    `user_type` varchar(255) NOT NULL DEFAULT 'guest',
    `user_notification` tinyint(1) NOT NULL DEFAULT 0,
    `admin_notification` tinyint(1) NOT NULL DEFAULT 0,
    `rating` FLOAT NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_unsubscribed_users`;
CREATE TABLE `#__gridbox_reviews_unsubscribed_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_likes_map`;
CREATE TABLE `#__gridbox_reviews_likes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL DEFAULT 0,
    `ip` varchar(255) NOT NULL DEFAULT "",
    `status` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_attachments`;
CREATE TABLE `#__gridbox_reviews_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `comment_id` int(11) NOT NULL DEFAULT 0,
    `name` varchar(255) NOT NULL DEFAULT "",
    `filename` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_emails`;
CREATE TABLE `#__gridbox_reviews_banned_emails` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_words`;
CREATE TABLE `#__gridbox_reviews_banned_words` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `word` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_reviews_banned_ip`;
CREATE TABLE `#__gridbox_reviews_banned_ip` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_fields_desktop_files`;
CREATE TABLE `#__gridbox_fields_desktop_files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `app_id` int(11) NOT NULL DEFAULT 0,
    `name` varchar(255) NOT NULL DEFAULT "",
    `filename` varchar(255) NOT NULL DEFAULT "",
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_payment_methods`;
CREATE TABLE `#__gridbox_store_payment_methods` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `image` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `settings` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_promo_codes`;
CREATE TABLE `#__gridbox_store_promo_codes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `code` varchar(255) NOT NULL DEFAULT '',
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `applies_to` varchar(255) NOT NULL DEFAULT '*',
    `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `limit` int(11) NOT NULL DEFAULT 0,
    `used` int(11) NOT NULL DEFAULT 0,
    `disable_sales` tinyint(1) NOT NULL DEFAULT 0,
    `access` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_promo_codes_map`;
CREATE TABLE `#__gridbox_store_promo_codes_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(255) NOT NULL DEFAULT "",
    `code_id` int(11) NOT NULL DEFAULT 0,
    `item_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_sales`;
CREATE TABLE `#__gridbox_store_sales` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `applies_to` varchar(255) NOT NULL DEFAULT '*',
    `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `access` int(11) NOT NULL DEFAULT 1,
    `cart_discount` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_sales_map`;
CREATE TABLE `#__gridbox_store_sales_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(255) NOT NULL DEFAULT "",
    `sale_id` int(11) NOT NULL DEFAULT 0,
    `item_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_shipping`;
CREATE TABLE `#__gridbox_store_shipping` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `price` varchar(255) NOT NULL DEFAULT '',
    `free` varchar(255) NOT NULL DEFAULT '',
    `options` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `carrier` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_products_fields`;
CREATE TABLE `#__gridbox_store_products_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_key` varchar(255) NOT NULL DEFAULT "",
    `field_type` varchar(255) NOT NULL DEFAULT "",
    `title` varchar(255) NOT NULL DEFAULT "",
    `options` text,
    `file_options` text,
    `required` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_products_fields_data`;
CREATE TABLE `#__gridbox_store_products_fields_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `field_id` int(11) NOT NULL DEFAULT 0,
    `option_key` varchar(255) NOT NULL DEFAULT "",
    `value` varchar(255) NOT NULL DEFAULT "",
    `color` varchar(255) NOT NULL DEFAULT '',
    `image` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_product_data`;
CREATE TABLE `#__gridbox_store_product_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `price` varchar(255) NOT NULL DEFAULT "",
    `sale_price` varchar(255) NOT NULL DEFAULT "",
    `sku` varchar(255) NOT NULL DEFAULT "",
    `min` varchar(255) NOT NULL DEFAULT "",
    `stock` varchar(255) NOT NULL DEFAULT "",
    `variations` text,
    `extra_options` text,
    `product_type` varchar(255) NOT NULL DEFAULT "",
    `digital_file` text,
    `subscription` text,
    `dimensions` text,
    `booking` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_product_variations_map`;
CREATE TABLE `#__gridbox_store_product_variations_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `field_id` int(11) NOT NULL DEFAULT 0,
    `option_key` varchar(255) NOT NULL DEFAULT "",
    `images` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    `order_group` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_customer_info`;
CREATE TABLE `#__gridbox_store_customer_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `required` tinyint(1) NOT NULL DEFAULT 0,
    `invoice` tinyint(1) NOT NULL DEFAULT 0,
    `options` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_customer_info_data`;
CREATE TABLE `#__gridbox_store_customer_info_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11),
    `field_id` int(11),
    `title` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `options` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_cart`;
CREATE TABLE `#__gridbox_store_cart` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `promo_id` int(11) NOT NULL DEFAULT 0,
    `country` varchar(255) NOT NULL DEFAULT '',
    `region` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_cart_products`;
CREATE TABLE `#__gridbox_store_cart_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT "",
    `quantity` int(11) NOT NULL DEFAULT 0,
    `renew_id` int(11) NOT NULL DEFAULT 0,
    `plan_key` varchar(255) NOT NULL DEFAULT "",
    `upgrade_id` int(11) NOT NULL DEFAULT 0,
    `extra_options` text,
    `booking` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_cart_attachments_map`;
CREATE TABLE `#__gridbox_store_cart_attachments_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `wishlist_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `option_id` int(11) NOT NULL DEFAULT 0,
    `order_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_product_attachments`;
CREATE TABLE `#__gridbox_store_product_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `date` datetime NOT NULL,
    `attachment_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_orders_status_history`;
CREATE TABLE `#__gridbox_store_orders_status_history` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `date` datetime,
    `status` varchar(255) NOT NULL DEFAULT 'new',
    `comment` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_orders`;
CREATE TABLE `#__gridbox_store_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` datetime,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `status` varchar(255) NOT NULL DEFAULT 'new',
    `published` tinyint(1) NOT NULL DEFAULT 0,
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `order_number` varchar(255) NOT NULL DEFAULT '#00000000',
    `subtotal` varchar(255) NOT NULL DEFAULT "",
    `tax` varchar(255) NOT NULL DEFAULT "",
    `total` varchar(255) NOT NULL DEFAULT "",
    `later` varchar(255) NOT NULL DEFAULT "0",
    `currency_symbol` varchar(255) NOT NULL DEFAULT "",
    `currency_position` varchar(255) NOT NULL DEFAULT "",
    `params` text,
    `tax_mode` varchar(255) NOT NULL DEFAULT 'excl',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_orders_discount`;
CREATE TABLE `#__gridbox_store_orders_discount` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `promo_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `code` varchar(255) NOT NULL DEFAULT "",
    `unit` varchar(255) NOT NULL DEFAULT '%',
    `discount` varchar(255) NOT NULL DEFAULT '',
    `value` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_orders_shipping`;
CREATE TABLE `#__gridbox_store_orders_shipping` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `shipping_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `price` varchar(255) NOT NULL DEFAULT "",
    `tax` varchar(255) NOT NULL DEFAULT "",
    `tax_title` varchar(255) NOT NULL DEFAULT '',
    `tax_rate` varchar(255) NOT NULL DEFAULT '',
    `type` varchar(255) NOT NULL DEFAULT 'flat',
    `carrier` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_orders_payment`;
CREATE TABLE `#__gridbox_store_orders_payment` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `payment_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_order_customer_info`;
CREATE TABLE `#__gridbox_store_order_customer_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `customer_id` int(11) NOT NULL DEFAULT 0,
    `cart_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    `value` text,
    `invoice` tinyint(1) NOT NULL DEFAULT 0,
    `options` text,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_order_products`;
CREATE TABLE `#__gridbox_store_order_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `image` varchar(255) NOT NULL DEFAULT "",
    `product_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT "",
    `quantity` int(11) NOT NULL DEFAULT 0,
    `price` varchar(255) NOT NULL DEFAULT "",
    `sale_price` varchar(255) NOT NULL DEFAULT "",
    `sku` varchar(255) NOT NULL DEFAULT "",
    `tax` varchar(255) NOT NULL DEFAULT '',
    `tax_title` varchar(255) NOT NULL DEFAULT '',
    `tax_rate` varchar(255) NOT NULL DEFAULT '',
    `net_price` varchar(255) NOT NULL DEFAULT '',
    `extra_options` text,
    `product_type` varchar(255) NOT NULL DEFAULT "",
    `product_token` varchar(255) NOT NULL DEFAULT "",
    `renew_id` int(11) NOT NULL DEFAULT 0,
    `plan_key` varchar(255) NOT NULL DEFAULT "",
    `upgrade_id` int(11) NOT NULL DEFAULT 0,
    `upgrade_price` varchar(255) NOT NULL DEFAULT "0",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_bookings`;
CREATE TABLE `#__gridbox_store_bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `start_date` varchar(255) NOT NULL DEFAULT '',
    `end_date` varchar(255) NOT NULL DEFAULT '',
    `start_time` varchar(255) NOT NULL DEFAULT '',
    `end_time` varchar(255) NOT NULL DEFAULT '',
    `guests` varchar(255) NOT NULL DEFAULT '',
    `price` varchar(255) NOT NULL DEFAULT '',
    `later` varchar(255) NOT NULL DEFAULT '',
    `prepaid` varchar(255) NOT NULL DEFAULT '',
    `paid` tinyint(1) NOT NULL DEFAULT 1,
    `unread` tinyint(1) NOT NULL DEFAULT 1,
    `reminded` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_bookings_blocks`;
CREATE TABLE `#__gridbox_store_bookings_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `start_date` varchar(255) NOT NULL DEFAULT '',
    `end_date` varchar(255) NOT NULL DEFAULT '',
    `start_time` varchar(255) NOT NULL DEFAULT '',
    `end_time` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_order_license`;
CREATE TABLE `#__gridbox_store_order_license` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `downloads` int(11) NOT NULL DEFAULT 0,
    `limit` varchar(255) NOT NULL DEFAULT "",
    `expires` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_subscriptions`;
CREATE TABLE `#__gridbox_store_subscriptions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `action` varchar(255) NOT NULL DEFAULT "",
    `user_groups` varchar(255) NOT NULL DEFAULT "",
    `date` varchar(255) NOT NULL DEFAULT "",
    `expires` varchar(255) NOT NULL DEFAULT "",
    `reminded` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_subscriptions_map`;
CREATE TABLE `#__gridbox_store_subscriptions_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `last_status` varchar(255) NOT NULL DEFAULT "completed",
    `start_date` varchar(255) NOT NULL DEFAULT "",
    `expires` varchar(255) NOT NULL DEFAULT "",
    `product_id` int(11) NOT NULL DEFAULT 0,
    `subscription_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_order_product_variations`;
CREATE TABLE `#__gridbox_store_order_product_variations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `type` varchar(255) NOT NULL DEFAULT "",
    `title` varchar(255) NOT NULL DEFAULT "",
    `value` varchar(255) NOT NULL DEFAULT "",
    `color` varchar(255) NOT NULL DEFAULT "",
    `image` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_user_info`;
CREATE TABLE `#__gridbox_store_user_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `customer_id` int(11) NOT NULL DEFAULT 0,
    `value` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_wishlist`;
CREATE TABLE `#__gridbox_store_wishlist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_wishlist_products`;
CREATE TABLE `#__gridbox_store_wishlist_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `wishlist_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `variation` varchar(255) NOT NULL DEFAULT "",
    `extra_options` text,
    `booking` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_badges`;
CREATE TABLE `#__gridbox_store_badges` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `color` varchar(255) NOT NULL DEFAULT "",
    `type` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_badges_map`;
CREATE TABLE `#__gridbox_store_badges_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `badge_id` int(11) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_related_products`;
CREATE TABLE `#__gridbox_store_related_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `related_id` int(11) NOT NULL DEFAULT 0,
    `order_list` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_countries`;
CREATE TABLE `#__gridbox_countries` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_country_states`;
CREATE TABLE `#__gridbox_country_states` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `country_id` varchar(255) NOT NULL DEFAULT "",
    `title` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_store_order_tracking`;
CREATE TABLE `#__gridbox_store_order_tracking` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT "",
    `number` text,
    `url` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_email_delay`;
CREATE TABLE `#__gridbox_email_delay` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `status` tinyint(1) NOT NULL DEFAULT 0,
    `order_id` int(11) NOT NULL,
    `notification` varchar(255) NOT NULL DEFAULT "",
    `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_seo_defaults`;
CREATE TABLE `#__gridbox_seo_defaults` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11)  NOT NULL DEFAULT 0,
    `item_type` varchar(255) NOT NULL DEFAULT "page",
    `meta_title` varchar(255) NOT NULL DEFAULT "",
    `meta_description` text,
    `share_image` varchar(255) NOT NULL DEFAULT '',
    `share_title` varchar(255) NOT NULL DEFAULT "",
    `share_description` text,
    `sitemap_include` varchar(255) NOT NULL DEFAULT '',
    `changefreq` varchar(255) NOT NULL DEFAULT '',
    `priority` varchar(255) NOT NULL DEFAULT '',
    `schema_markup` text,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_user_avatars`;
CREATE TABLE `#__gridbox_user_avatars` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL DEFAULT 0,
    `avatar` varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_pages_versions`;
CREATE TABLE `#__gridbox_pages_versions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `page_id` int(11) NOT NULL DEFAULT 0,
    `html` mediumtext,
    `items` mediumtext,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_tags_folders`;
CREATE TABLE `#__gridbox_tags_folders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `order_list` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_tags_folders_map`;
CREATE TABLE `#__gridbox_tags_folders_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) NOT NULL DEFAULT 1,
    `folder_id` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_submitted_items`;
CREATE TABLE `#__gridbox_submitted_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `submission_form` int(11) NOT NULL DEFAULT 0,
    `sended_published` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_category_page_map`;
CREATE TABLE `#__gridbox_category_page_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `category_id` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__gridbox_tags_folders` (`title`) VALUES
('All');

INSERT INTO `#__gridbox_store_badges` (`title`, `color`, `type`) VALUES
('', '#f64231', 'sale'),
('Sale', '#ff7a2f', ''),
('New', '#34dca2', ''),
('Hot', '#ffc700', '');

INSERT INTO `#__gridbox_store_customer_info` (`title`, `type`, `required`, `options`, `order_list`, `invoice`) VALUES
('First name', 'text', 1, '{"placeholder":"","html":"","options":[],"width":"50"}', 1, 1),
('Contact Information', 'headline', 0, '{"placeholder":"","html":"","options":[],"width":"100"}', 0, 0),
('Last name', 'text', 0, '{"placeholder":"","html":"","options":[],"width":"50"}', 2, 1),
('Phone', 'text', 0, '{"placeholder":"","html":"","options":[],"width":"100"}', 3, 1),
('Email', 'email', 1, '{"placeholder":"","html":"","options":[],"width":"100"}', 4, 1),
('Shipping Address', 'headline', 0, '{"placeholder":"","html":"","options":[],"width":"100"}', 5, 0),
('Address', 'text', 1, '{"placeholder":"","html":"","options":[],"width":"100"}', 6, 1),
('Apartment, suite, etc. (optional)', 'text', 0, '{"placeholder":"","html":"","options":[],"width":"100"}', 7, 1),
('City', 'text', 1, '{"placeholder":"","html":"","options":[],"width":"50"}', 8, 1),
('Zip Code', 'text', 0, '{"placeholder":"","html":"","options":[],"width":"50"}', 10, 1),
('', 'acceptance', 1, '{"placeholder":"","html":"I have read and agree to the <a href=\\"#\\" target=\\"_blank\\">Terms and Conditions<\\/a>","options":[],"width":"100"}', 11, 0);

INSERT INTO `#__gridbox_system_pages`(`title`, `alias`, `type`, `theme`, `order_list`, `page_options`, `html`, `items`, `fonts`) VALUES
('404 Error Page', '', '404', 0, 1, '{"enable_header":false}', '', '', ''),
('Coming Soon Page', '', 'offline', 0, 1, '{}', '', '', ''),
('Search Results Page', 'search', 'search', 0, 1, '{}', '', '', ''),
('Preloader', '', 'preloader', 0, 1, '{}', '', '', ''),
('Checkout Page', 'checkout', 'checkout', 0, 1, '{}', '', '', ''),
('Thank You Page', 'thank-you', 'thank-you-page', 0, 1, '{}', '', '', ''),
('Store Search Results Page', 'store-search', 'store-search', 0, 1, '{}', '', '', ''),
('Submission form', 'submission-form', 'submission-form', 0, 1, '{"premoderation":true,"author":true,"access":1,"emails":true,"submited_email":true,"published_email":true}', '', '', '');

INSERT INTO `#__gridbox_website` (`favicon`, `header_code`, `body_code`, `breakpoints`) VALUES
('', '', '', '{"laptop":1440, "tablet":1280,"tablet-portrait":1024,"phone":768,"phone-portrait":420,"menuBreakpoint":1024}');

INSERT INTO `#__gridbox_api` (`service`, `key`) VALUES
('google_maps', ''),
('library_font', ''),
('user_colors', '{"0":"#eb523c","1":"#f65954","2":"#ec821a","3":"#f5c500","4":"#34dca2","5":"#20364c","6":"#32495f","7":"#0075a9","8":"#1996dd","9":"#6cc6fa"}'),
('openweathermap', ''),
('yandex_maps', ''),
('gridbox_sitemap', ''),
('store', '{}'),
('booking_calendar', '{}'),
('booking_colors', '{}'),
('exchangerates_data', '{}');

INSERT INTO `#__gridbox_api` (`service`, `key`, `type`, `title`) VALUES
('exchangerates', '', 'integration', 'Exchangerates'),
('inpost', '', 'integration', 'InPost'),
('novaposhta', '', 'integration', 'Nova Poshta');

INSERT INTO `#__gridbox_plugins` (`title`, `image`, `type`, `joomla_constant`) VALUES
('ba-image', 'flaticon-picture', 'content', 'IMAGE'),
('ba-text', 'flaticon-file', 'content', 'TEXT'),
('ba-button', 'plugins-button', 'content', 'BUTTON'),
('ba-logo', 'flaticon-diamond', 'navigation', 'LOGO'),
('ba-menu', 'flaticon-app', 'navigation', 'MENU'),
('ba-modules', 'plugins-modules', '3rd-party-plugins', 'JOOMLA_MODULES'),
('ba-forms', 'plugins-forms', '3rd-party-plugins', 'BALBOOA_FORMS'),
('ba-gallery', 'plugins-gallery', '3rd-party-plugins', 'BALBOOA_GALLERY'),
('ba-hotspot', 'plugins-hotspot', 'info', 'HOTSPOT');

INSERT INTO `#__gridbox_fonts` (`font`, `styles`) VALUES
('Open+Sans', 300),
('Open+Sans', 400),
('Open+Sans', 700),
('Poppins', 300),
('Poppins', 400),
('Poppins', 500),
('Poppins', 600),
('Poppins', 700),
('Roboto', 300),
('Roboto', 400),
('Roboto', 500),
('Roboto', 700),
('Roboto', 900),
('Lato', 300),
('Lato', 400),
('Lato', 700),
('Slabo+27px', 400),
('Oswald', 300),
('Oswald', 400),
('Oswald', 600),
('Roboto+Condensed', 300),
('Roboto+Condensed', 400),
('Roboto+Condensed', 700),
('PT+Sans', 400),
('PT+Sans', 700),
('Montserrat', 200),
('Montserrat', 300),
('Montserrat', 400),
('Montserrat', 700),
('Playfair+Display', 400),
('Playfair+Display', 700),
('Comfortaa', 300),
('Comfortaa', 400),
('Comfortaa', 700);