ALTER TABLE `#__baforms_forms` ADD COLUMN `telegram_token` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `payu_biz_merchant` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `payu_biz_salt` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_items` ADD COLUMN `custom` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_items` ADD COLUMN `options` text NOT NULL;