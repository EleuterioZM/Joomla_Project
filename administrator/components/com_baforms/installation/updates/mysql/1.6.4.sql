ALTER TABLE `#__baforms_forms` ADD COLUMN `stripe_secret_key` varchar(255) NOT NULL;
ALTER TABLE `#__baforms_forms` ADD COLUMN `load_jquery` tinyint(1) NOT NULL DEFAULT 1;