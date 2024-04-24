INSERT INTO `#__gridbox_system_pages`(`title`, `alias`, `type`, `theme`, `order_list`, `page_options`, `html`, `items`, `fonts`) VALUES
('Submission form', 'submission-form', 'submission-form', 0, 1, '{"premoderation":true,"author":true,"access":1,"emails":true,"submited_email":true,"published_email":true}', '', '', '');
ALTER TABLE `#__gridbox_pages` ADD `user_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__gridbox_pages` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__gridbox_pages` CHANGE `params` `params` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `#__gridbox_submitted_items`;
CREATE TABLE `#__gridbox_submitted_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_id` int(11) NOT NULL DEFAULT 0,
    `submission_form` int(11) NOT NULL DEFAULT 0,
    `sended_published` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;