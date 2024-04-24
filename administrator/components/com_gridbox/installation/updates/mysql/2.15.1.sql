DROP TABLE IF EXISTS `#__gridbox_tags_folders`;
CREATE TABLE `#__gridbox_tags_folders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT "",
    `order_list` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_tags_folders_map`;
CREATE TABLE `#__gridbox_tags_folders_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) NOT NULL DEFAULT 1,
    `folder_id` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__gridbox_tags_folders` (`title`) VALUES
('All');