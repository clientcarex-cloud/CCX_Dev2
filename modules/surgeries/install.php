<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'surgery_types')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'surgery_types` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `description` text,
      `price` decimal(15,2) NOT NULL DEFAULT "0.00",
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgery_types`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgery_types`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'surgeries')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'surgeries` (
      `id` int(11) NOT NULL,
      `patient_id` int(11) NOT NULL,
      `surgery_type_id` int(11) NOT NULL,
      `surgeon_id` int(11) NOT NULL,
      `assistant_surgeon_id` int(11) DEFAULT NULL,
      `anesthetist_id` int(11) DEFAULT NULL,
      `surgery_date` date NOT NULL,
      `surgery_time` time NOT NULL,
      `ot_room_number` varchar(100) DEFAULT NULL,
      `status` varchar(50) NOT NULL DEFAULT "scheduled",
      `notes` text,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
} else {
  // Check and add missing columns if table exists (simple migration)

  // Ensure surgery_type_id exists
  if (!$CI->db->field_exists('surgery_type_id', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `surgery_type_id` int(11) NOT NULL;');
  }

  // Ensure patient_id exists (just in case)
  if (!$CI->db->field_exists('patient_id', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `patient_id` int(11) NOT NULL;');
  }

  // Ensure surgeon_id exists
  if (!$CI->db->field_exists('surgeon_id', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `surgeon_id` int(11) NOT NULL;');
  }
  // Ensure assistant_surgeon_id exists
  if (!$CI->db->field_exists('assistant_surgeon_id', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `assistant_surgeon_id` int(11) DEFAULT NULL;');
  }
  // Ensure anesthetist_id exists
  if (!$CI->db->field_exists('anesthetist_id', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `anesthetist_id` int(11) DEFAULT NULL;');
  }

  // Ensure surgery_date exists
  if (!$CI->db->field_exists('surgery_date', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `surgery_date` date NOT NULL;');
  }
  // Ensure surgery_time exists
  if (!$CI->db->field_exists('surgery_time', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `surgery_time` time NOT NULL;');
  }
  // Ensure ot_room_number exists
  if (!$CI->db->field_exists('ot_room_number', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `ot_room_number` varchar(100) DEFAULT NULL;');
  }
  // Ensure status exists
  if (!$CI->db->field_exists('status', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `status` varchar(50) NOT NULL DEFAULT "scheduled";');
  }
  // Ensure notes exists
  if (!$CI->db->field_exists('notes', db_prefix() . 'surgeries')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'surgeries` ADD COLUMN `notes` text DEFAULT NULL;');
  }
}
