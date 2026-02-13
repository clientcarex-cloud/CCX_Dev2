<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'transcriptor')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'transcriptor` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `patient_test_id` int(11) NOT NULL,
      `staff_id` int(11) NOT NULL,
      `content` longtext DEFAULT NULL,
      `status` varchar(50) DEFAULT "Draft",
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('template_type', db_prefix() . 'transcriptor')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'transcriptor` ADD `template_type` VARCHAR(20) DEFAULT "word" AFTER `content`');
}

if (!$CI->db->table_exists(db_prefix() . 'transcriptor_params')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'transcriptor_params` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `transcription_id` int(11) NOT NULL,
      `parameter_id` int(11) DEFAULT 0,
      `parameter_name` varchar(255) NOT NULL,
      `result_value` text DEFAULT NULL,
      `unit` varchar(50) DEFAULT NULL,
      `referral_range` varchar(255) DEFAULT NULL,
      `is_bold` int(11) DEFAULT 0,
      `sort_order` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
