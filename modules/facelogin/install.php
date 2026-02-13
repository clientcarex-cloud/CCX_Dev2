<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

add_option('facelogin_purchase_code', '');
add_option('facelogin_purchase_is_valid', 1);


$sql_query = "CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_data` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,  
    `uniq_id` INT(11) DEFAULT NULL,
    `user_type` ENUM('staff', 'client') NOT NULL,
    `is_active` INT(11) DEFAULT 1,
    `created_by` INT(11) DEFAULT NULL, 
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);";

$CI->db->query($sql_query);

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_links` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `staff_id` INT(11) NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_used_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `token` (`token`),
    INDEX `staff_idx` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . db_prefix() . "face_activity_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `staff_id` INT(11) NOT NULL,
    `date` DATE NOT NULL,
    `active_seconds` INT(11) NOT NULL DEFAULT 0,
    `idle_seconds` INT(11) NOT NULL DEFAULT 0,
    `last_heartbeat` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `staff_date` (`staff_id`, `date`),
    INDEX `date_idx` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
