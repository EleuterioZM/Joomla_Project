ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_label` varchar(255) NOT NULL DEFAULT 'Save and Continue';
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_size` varchar(30) NOT NULL DEFAULT '13';
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_weight` varchar(10) NOT NULL DEFAULT 'normal';
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_align` varchar(20) NOT NULL DEFAULT 'center';
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_color` varchar(50) NOT NULL DEFAULT '#009ddc';
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_popup_title` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_popup_message` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_subject` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `save_continue_email` text NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `ccavenue_merchant_id` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `ccavenue_working_key` varchar(255) NOT NULL;
DROP TABLE IF EXISTS `#__baforms_tokens`;
CREATE TABLE `#__baforms_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `token` varchar(255) NOT NULL,
    `data` text NOT NULL,
    `expires` varchar(255) NOT NULL,
    `ip` varchar(50) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;