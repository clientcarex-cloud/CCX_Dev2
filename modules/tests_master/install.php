<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Items Table Alterations
if (!$CI->db->field_exists('code', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `code` VARCHAR(50) NULL AFTER `description`');
}

if (!$CI->db->field_exists('department_id', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `department_id` INT(11) NULL DEFAULT 0');
}

if (!$CI->db->field_exists('active_template_type', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `active_template_type` VARCHAR(20) DEFAULT "word"');
}

if (!$CI->db->field_exists('fixed_template_remarks', db_prefix() . 'items')) {
    // Keeping this for backward compatibility if data exists, but mostly moved to tests_fixed_templates
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `fixed_template_remarks` TEXT NULL');
}

if (!$CI->db->field_exists('is_blood_sample_required', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `is_blood_sample_required` INT(11) DEFAULT 0');
}

if (!$CI->db->field_exists('is_price_changable', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `is_price_changable` INT(11) DEFAULT 0');
}

if (!$CI->db->field_exists('is_authorization_required', db_prefix() . 'items')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` ADD `is_authorization_required` INT(11) DEFAULT 0');
}

// 1. Word Templates (Renamed from tests_templates)
if (!$CI->db->table_exists(db_prefix() . 'tests_word_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'tests_word_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `test_id` int(11) NOT NULL,
      `template_name` varchar(255) NOT NULL,
      `template_content` longtext DEFAULT NULL,
      `created_at` datetime DEFAULT current_timestamp(),
      `is_default` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 2. Fixed Templates Metadata
if (!$CI->db->table_exists(db_prefix() . 'tests_fixed_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'tests_fixed_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `test_id` int(11) NOT NULL,
      `template_name` varchar(255) NOT NULL,
      `remarks` text DEFAULT NULL,
      `is_default` int(11) DEFAULT 0,
      `created_at` datetime DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// 3. Fixed Template Parameters
if (!$CI->db->table_exists(db_prefix() . 'tests_params')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'tests_params` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `test_id` int(11) NOT NULL DEFAULT 0, 
      `fixed_template_id` int(11) DEFAULT 0,
      `parameter_name` varchar(255) NOT NULL,
      `default_value` text DEFAULT NULL,
      `unit` varchar(50) DEFAULT NULL,
      `referral_range` varchar(255) DEFAULT NULL,
      `normal_range` varchar(255) DEFAULT NULL,
      `method` varchar(100) DEFAULT NULL,
      `formula` varchar(255) DEFAULT NULL,
      `machine_code` varchar(100) DEFAULT NULL,
      `group_name` varchar(100) DEFAULT NULL,
      `is_bold` int(11) DEFAULT 0,
      `hide_units_range` int(11) DEFAULT 0,
      `sort_order` int(11) DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    // Alteration check for existing install
    if (!$CI->db->field_exists('fixed_template_id', db_prefix() . 'tests_params')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'tests_params` ADD `fixed_template_id` INT(11) DEFAULT 0');
    }
}
