ALTER TABLE `#__gridbox_store_sales` ADD `cart_discount` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__gridbox_store_shipping` ADD `carrier` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_store_orders_shipping` ADD `carrier` varchar(255) NOT NULL DEFAULT '';
INSERT INTO `#__gridbox_api` (`service`, `key`, `type`, `title`) VALUES
('inpost', '', 'integration', 'InPost'),
('novaposhta', '', 'integration', 'Nova Poshta');