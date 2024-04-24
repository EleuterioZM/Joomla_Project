ALTER TABLE `#__baforms_submissions` CHANGE `mesage` `message` TEXT NOT NULL;
ALTER TABLE `#__baforms_items` ADD `parent` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_items` ADD `key` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_items` ADD `type` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_columns` ADD `parent` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_columns` ADD `key` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_columns` ADD `width` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD `pdf_submissions` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD `campaign_monitor_fields` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD `getresponse_fields` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD `zoho_crm_fields` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD `google_drive` text NOT NULL;

DROP TABLE IF EXISTS `#__baforms_submissions_attachments`;
CREATE TABLE `#__baforms_submissions_attachments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `submission_id` int(11) NOT NULL,
    `form_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__baforms_pages`;
CREATE TABLE `#__baforms_pages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `form_id` int(11) NOT NULL,
    `key` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `columns_order` text NOT NULL,
    `order_index` int(11) NOT NULL,
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

CREATE TABLE IF NOT EXISTS `#__baforms_templates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `group` varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    `data` text NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__baforms_api` (`service`, `key`) VALUES
('paypal', '{"email":"","environment":"","return_url":""}'),
('twocheckout', '{"account":"","environment":"","return_url":""}'),
('mailchimp', ''),
('stripe', '{"api_key":"","secret_key":"","return_url":""}'),
('authorize', '{"login_id":"","transaction_key":"","environment":"","return_url":""}');