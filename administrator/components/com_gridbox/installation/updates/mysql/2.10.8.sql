ALTER TABLE `#__gridbox_app` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `alias` `alias` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `theme` `theme` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_app` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `page_layout` `page_layout` mediumtext;
ALTER TABLE `#__gridbox_app` CHANGE `page_items` `page_items` mediumtext;
ALTER TABLE `#__gridbox_app` CHANGE `page_fonts` `page_fonts` text;
ALTER TABLE `#__gridbox_app` CHANGE `app_fonts` `app_fonts` text;
ALTER TABLE `#__gridbox_app` CHANGE `app_layout` `app_layout` mediumtext;
ALTER TABLE `#__gridbox_app` CHANGE `app_items` `app_items` mediumtext;
ALTER TABLE `#__gridbox_app` CHANGE `image` `image` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `meta_title` `meta_title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `meta_description` `meta_description` text;
ALTER TABLE `#__gridbox_app` CHANGE `meta_keywords` `meta_keywords` text;
ALTER TABLE `#__gridbox_app` CHANGE `post_editor_wrapper` `post_editor_wrapper` text;
ALTER TABLE `#__gridbox_app` CHANGE `description` `description` text;
ALTER TABLE `#__gridbox_app` CHANGE `fields_groups` `fields_groups` text;
ALTER TABLE `#__gridbox_app` CHANGE `share_title` `share_title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_app` CHANGE `share_description` `share_description` text;
ALTER TABLE `#__gridbox_store_payment_methods` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_payment_methods` CHANGE `image` `image` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_payment_methods` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_payment_methods` CHANGE `settings` `settings` text;
ALTER TABLE `#__gridbox_store_promo_codes` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_promo_codes_map` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_promo_codes_map` CHANGE `code_id` `code_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_promo_codes_map` CHANGE `item_id` `item_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_shipping` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_related_products` CHANGE `product_id` `product_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_related_products` CHANGE `related_id` `related_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_comments` CHANGE `page_id` `page_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_comments` CHANGE `status` `status` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments` CHANGE `message` `message` text;
ALTER TABLE `#__gridbox_comments` CHANGE `email` `email` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments` CHANGE `avatar` `avatar` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_unsubscribed_users` CHANGE `user` `user` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_likes_map` CHANGE `comment_id` `comment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_comments_likes_map` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_likes_map` CHANGE `status` `status` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_attachments` CHANGE `comment_id` `comment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_comments_attachments` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_attachments` CHANGE `filename` `filename` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_attachments` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_banned_emails` CHANGE `email` `email` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_banned_words` CHANGE `word` `word` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_comments_banned_ip` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews` CHANGE `page_id` `page_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_reviews` CHANGE `status` `status` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews` CHANGE `message` `message` text;
ALTER TABLE `#__gridbox_reviews` CHANGE `email` `email` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews` CHANGE `avatar` `avatar` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_unsubscribed_users` CHANGE `user` `user` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_likes_map` CHANGE `comment_id` `comment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_reviews_likes_map` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_likes_map` CHANGE `status` `status` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_attachments` CHANGE `comment_id` `comment_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_reviews_attachments` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_attachments` CHANGE `filename` `filename` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_attachments` CHANGE `type` `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_banned_emails` CHANGE `email` `email` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_banned_words` CHANGE `word` `word` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_reviews_banned_ip` CHANGE `ip` `ip` varchar(255) NOT NULL DEFAULT "";