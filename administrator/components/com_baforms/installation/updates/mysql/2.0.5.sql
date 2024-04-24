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