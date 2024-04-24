ALTER TABLE `#__baforms_forms` ADD `activecampaign_fields` text NOT NULL;
INSERT INTO `#__baforms_api` (`service`, `key`) VALUES
('activecampaign', '{"account":"","api_key":""}'),
('mollie', '{"api_key":"","return_url":""}');