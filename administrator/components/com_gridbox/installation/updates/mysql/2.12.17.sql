ALTER TABLE `#__gridbox_api` ADD COLUMN `title` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_api` ADD COLUMN `type` varchar(255) NOT NULL DEFAULT "";
ALTER TABLE `#__gridbox_store_sales` ADD COLUMN `access` int(11) NOT NULL DEFAULT 1;
ALTER TABLE `#__gridbox_store_promo_codes` ADD COLUMN `access` int(11) NOT NULL DEFAULT 1;