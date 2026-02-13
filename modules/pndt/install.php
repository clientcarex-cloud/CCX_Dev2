<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create tblpndt if it doesn't exist
if (!$CI->db->table_exists(db_prefix() . 'pndt')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'pndt` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `patient_test_id` int(11) NOT NULL,
      `staff_id` int(11) NOT NULL,
      `content` longtext DEFAULT NULL,
      `status` varchar(50) DEFAULT "Draft",
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    // If it exists, add missing columns
    if (!$CI->db->field_exists('patient_test_id', db_prefix() . 'pndt')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `patient_test_id` int(11) NOT NULL AFTER `id`');
    }
    if (!$CI->db->field_exists('staff_id', db_prefix() . 'pndt')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `staff_id` int(11) NOT NULL AFTER `patient_test_id`');
    }
    if (!$CI->db->field_exists('content', db_prefix() . 'pndt')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `content` longtext DEFAULT NULL AFTER `staff_id`');
    }
    if (!$CI->db->field_exists('status', db_prefix() . 'pndt')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'pndt` ADD `status` varchar(50) DEFAULT "Draft" AFTER `content`');
    }
}

// Add father_husband_name to patients_extra if it doesn't exist
if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {
    if (!$CI->db->field_exists('father_husband_name', db_prefix() . 'patients_extra')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `father_husband_name` VARCHAR(150) DEFAULT NULL AFTER `mr_number`');
    }
}
