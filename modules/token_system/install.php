<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'tokens')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'tokens` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `token_number` varchar(50) NOT NULL,
      `patient_id` int(11) DEFAULT NULL,
      `patient_name` varchar(255) DEFAULT NULL,
      `status` int(11) NOT NULL DEFAULT 0,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      `staff_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Update tbltokens
if (!$CI->db->field_exists('doctor_id', db_prefix() . 'tokens')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tokens` ADD `doctor_id` INT DEFAULT 0 NOT NULL AFTER `staff_id`;');
}

if (!$CI->db->table_exists(db_prefix() . 'token_displays')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'token_displays` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `ad_type` varchar(50) DEFAULT "none",
      `ad_url` text,
      `doctor_id` int(11) DEFAULT 0,
      `layout` varchar(50) DEFAULT "modern",
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Update tbltoken_displays if needed (for safety if table existed before layout/doctor_id)
if (!$CI->db->field_exists('doctor_id', db_prefix() . 'token_displays')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'token_displays` ADD `doctor_id` INT DEFAULT 0 AFTER `ad_url`;');
}
if (!$CI->db->field_exists('layout', db_prefix() . 'token_displays')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'token_displays` ADD `layout` VARCHAR(50) DEFAULT "modern" AFTER `doctor_id`;');
}
