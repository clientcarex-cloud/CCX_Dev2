<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'coupons')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'coupons` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `code` varchar(191) NOT NULL,
      `name` varchar(191) NOT NULL,
      `type` int(11) NOT NULL COMMENT "1: Percentage, 2: Fixed Amount, 3: Volume, 4: Time Period, 5: Random, 6: Festival",
      `amount` decimal(15,2) DEFAULT 0.00,
      `type_settings` text DEFAULT NULL,
      `start_date` date DEFAULT NULL,
      `end_date` date DEFAULT NULL,
      `max_uses` int(11) DEFAULT 0,
      `max_uses_per_client` int(11) DEFAULT 0,
      `active` int(11) DEFAULT 1,
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
