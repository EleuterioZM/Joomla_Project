CREATE TABLE IF NOT EXISTS `#__baforms_forms` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `published` tinyint(1) NOT NULL DEFAULT 1,
    `telegram_token` varchar(255) NOT NULL,
    `google_sheets` text NOT NULL,
    `google_drive` text NOT NULL,
    `acym_lists` text NOT NULL,
    `acym_fields_map` text NOT NULL,
    `mailchimp_list_id` varchar(255) NOT NULL,
    `mailchimp_fields_map` text NOT NULL,
    `pdf_submissions` text NOT NULL,
    `campaign_monitor_fields` text NOT NULL,
    `getresponse_fields` text NOT NULL,
    `zoho_crm_fields` text NOT NULL,
    `activecampaign_fields` text NOT NULL,
    
    `title_settings` text NOT NULL,
    `form_settings` text NOT NULL,
    `ordering` int(11) NOT NULL,
    `save_submissions` tinyint(1) NOT NULL DEFAULT 1,
    `attach_uploaded_files` tinyint(1) NOT NULL DEFAULT 1,
    `alow_captcha` varchar(40) NOT NULL,
    `display_title` tinyint(1) NOT NULL DEFAULT 1,
    `sent_massage` text NOT NULL,
    `error_massage` text NOT NULL,
    `redirect_url` varchar(255) NOT NULL,
    `email_recipient` varchar(255) NOT NULL,
    `email_subject` varchar(255) NOT NULL,
    `email_body` text NOT NULL,
    `sender_name` varchar(255) NOT NULL,
    `sender_email` varchar(255) NOT NULL,
    `reply_subject` varchar(255) NOT NULL,
    `reply_body` text NOT NULL,
    `display_popup` tinyint(1) NOT NULL DEFAULT 0,
    `button_lable` varchar(255) NOT NULL,
    `button_position` varchar(40) NOT NULL,
    `button_bg` varchar(40) NOT NULL,
    `button_color` varchar(40) NOT NULL,
    `button_font_size` int(5) NOT NULL DEFAULT 14,
    `button_border` int(5) NOT NULL DEFAULT 3,
    `button_weight` varchar(10) NOT NULL DEFAULT 'normal',
    `display_submit` tinyint(1) NOT NULL DEFAULT 1,
    `submit_embed` text NOT NULL,
    `message_bg_rgba` varchar(255) NOT NULL,
    `message_color_rgba` varchar(255) NOT NULL,
    `dialog_color_rgba` varchar(255) NOT NULL,
    `add_sender_email` varchar(255) NOT NULL DEFAULT 0,
    `copy_submitted_data` tinyint(1) NOT NULL DEFAULT 0,
    `modal_width` varchar(255) NOT NULL DEFAULT '500',
    `display_total` tinyint(1) NOT NULL DEFAULT 0,
    `currency_code` varchar(255) NOT NULL,
    `currency_symbol` varchar(255) NOT NULL,
    `payment_methods` varchar(255) NOT NULL,
    `return_url` varchar(255) NOT NULL,
    `cancel_url` varchar(255) NOT NULL,
    `paypal_email` varchar(255) NOT NULL,
    `payment_environment` varchar(255) NOT NULL,
    `seller_id` varchar(255) NOT NULL,
    `skrill_email` varchar(255) NOT NULL,
    `webmoney_purse` varchar(255) NOT NULL,
    `check_ip` tinyint(1) NOT NULL DEFAULT 0,
    `payu_api_key` varchar(255) NOT NULL,
    `payu_merchant_id` varchar(255) NOT NULL,
    `payu_account_id` varchar(255) NOT NULL,
    `button_type` varchar(255) NOT NULL,
    `email_letter` mediumtext NOT NULL,
    `display_cart` tinyint(1) NOT NULL DEFAULT 0,
    `currency_position` varchar(255) NOT NULL DEFAULT 'before',
    `multiple_payment` tinyint(1) NOT NULL DEFAULT 0,
    `custom_payment` varchar(255) NOT NULL DEFAULT 'Custom Payment',
    `mailchimp_api_key` varchar(255) NOT NULL,
    `stripe_api_key` varchar(255) NOT NULL,
    `stripe_secret_key` varchar(255) NOT NULL,
    `stripe_image` varchar(255) NOT NULL,
    `stripe_name` varchar(255) NOT NULL,
    `stripe_description` varchar(255) NOT NULL,
    `theme_color` varchar(255) NOT NULL DEFAULT '#009ddc',
    `email_options` text NOT NULL,
    `mollie_api_key` varchar(255) NOT NULL,
    `payu_biz_merchant` varchar(255) NOT NULL,
    `payu_biz_salt` varchar(255) NOT NULL,
    `save_continue` tinyint(1) NOT NULL DEFAULT 0,
    `save_continue_label` varchar(255) NOT NULL DEFAULT 'Save and Continue',
    `save_continue_size` varchar(30) NOT NULL DEFAULT '13',
    `save_continue_weight` varchar(10) NOT NULL DEFAULT 'normal',
    `save_continue_align` varchar(20) NOT NULL DEFAULT 'center',
    `save_continue_color` varchar(50) NOT NULL DEFAULT '#009ddc',
    `save_continue_popup_title` varchar(255) NOT NULL,
    `save_continue_popup_message` text NOT NULL,
    `save_continue_subject` varchar(255) NOT NULL,
    `save_continue_email` text NOT NULL,
    `ccavenue_merchant_id` varchar(255) NOT NULL,
    `ccavenue_working_key` varchar(255) NOT NULL,
    `load_jquery` tinyint(1) NOT NULL DEFAULT 1,
    `acymailing_lists` text NOT NULL,
    `acymailing_fields_map` text NOT NULL,
    `yandex_shopId` varchar(255) NOT NULL,
    `yandex_scid` varchar(255) NOT NULL,
    `barion_poskey` varchar(255) NOT NULL,
    `barion_email` varchar(255) NOT NULL,
    `payu_pl_pos_id` varchar(255) NOT NULL,
    `payu_pl_second_key` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_forms_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `design` text NOT NULL,
    `navigation` text NOT NULL,
    `condition_logic` text NOT NULL,
    `css` text NOT NULL,
    `js` text NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `key` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `columns_order` text NOT NULL,
    `order_index` int(11) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_submissions_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `submission_id` int(11) NOT NULL,
    `form_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_columns` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `settings` text NOT NULL,
    `parent` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    `width` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `column_id` int(11) NOT NULL,
    `settings` text NOT NULL,
    `custom` varchar(255) NOT NULL,
    `options` text NOT NULL,
    `parent` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_submissions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `date_time` varchar(255) NOT NULL,
    `submission_state` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_api` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service` varchar(255) NOT NULL,
    `key` text NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `token` varchar(255) NOT NULL,
    `data` text NOT NULL,
    `expires` varchar(255) NOT NULL,
    `ip` varchar(50) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_templates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `group` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    `data` text NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_poll_results` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `value` varchar(255) NOT NULL,
    `ip` varchar(255) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__baforms_poll_statistic` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `submission_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `data` text,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__baforms_api` (`service`, `key`) VALUES
('google_maps', ''),
('google_sheets', '{"client_id":"", "client_secret":"", "code":"", "accessToken": ""}'),
('paypal', '{"email":"","environment":"","return_url":""}'),
('twocheckout', '{"account":"","environment":"","return_url":""}'),
('mailchimp', ''),
('stripe', '{"api_key":"","secret_key":"","return_url":""}'),
('authorize', '{"login_id":"","transaction_key":"","environment":"","return_url":""}'),
('activecampaign', '{"account":"","key":""}'),
('mollie', '{"api_key":"","return_url":""}'),
('payu_latam', '{"api_key":"","merchant_id":"","account_id":"","environment":"","return_url":""}'),
('yandex_kassa', '{"shop_id":"","scid":"","environment":"","return_url":""}'),
('redsys', '{"merchant":"","transaction":"","terminal":"","signature":"","return_url":""}'),
('payfast', '{"merchant_id":"","merchant_key":"","environment":"","return_url":""}'),
('paypal_sdk', '{"client_id":"","return_url":""}'),
('hcaptcha', '{"site_key":"","secret_key":"","theme":"","invisible":false}');