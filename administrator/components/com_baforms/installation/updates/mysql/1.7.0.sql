ALTER TABLE `#__baforms_forms` ADD COLUMN `acymailing_lists` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `acymailing_fields_map` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `yandex_shopId` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `yandex_scid` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `google_sheets` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `barion_poskey` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `barion_email` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `payu_pl_pos_id` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `payu_pl_second_key` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_api` CHANGE `key` `key` text NOT NULL;
INSERT INTO `#__baforms_api` (`service`, `key`) VALUES
('google_sheets', '{"code":"", "accessToken": ""}');