<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'web_integration_forms')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'web_integration_forms` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `form_key` varchar(32) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms`
      ADD PRIMARY KEY (`id`),
      ADD UNIQUE KEY `form_key` (`form_key`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}


if (!$CI->db->field_exists('created_by', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `created_by` INT(11) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('form_category', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `form_category` VARCHAR(50) NOT NULL DEFAULT \'Lead\';');
}

if (!$CI->db->field_exists('secret_key', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `secret_key` VARCHAR(64) DEFAULT NULL;');
}

if (!$CI->db->field_exists('description', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `description` TEXT DEFAULT NULL;');
}

if (!$CI->db->field_exists('link_with_leads', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `link_with_leads` TINYINT(1) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('link_with_appointments', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `link_with_appointments` TINYINT(1) NOT NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('success_message', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `success_message` TEXT DEFAULT NULL;');
}

if (!$CI->db->table_exists(db_prefix() . 'web_integration_form_fields')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'web_integration_form_fields` (
      `id` int(11) NOT NULL,
      `form_id` int(11) NOT NULL,
      `field_id` varchar(50) NOT NULL,
      `is_visible` tinyint(1) NOT NULL DEFAULT 1,
      `is_required` tinyint(1) NOT NULL DEFAULT 0,
      `custom_label` varchar(150) DEFAULT NULL,
      `field_order` int(11) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_form_fields`
      ADD PRIMARY KEY (`id`),
      ADD KEY `form_id` (`form_id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_form_fields`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'web_integration_entries')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'web_integration_entries` (
      `id` int(11) NOT NULL,
      `form_id` int(11) NOT NULL,
      `name` varchar(150) DEFAULT NULL,
      `age` int(11) DEFAULT NULL,
      `gender` varchar(20) DEFAULT NULL,
      `mobile_number` varchar(50) DEFAULT NULL,
      `whatsapp_number` varchar(50) DEFAULT NULL,
      `treatment` text DEFAULT NULL,
      `doctor` varchar(150) DEFAULT NULL,
      `consultation_datetime` datetime DEFAULT NULL,
      `email` varchar(150) DEFAULT NULL,
      `attachment` varchar(255) DEFAULT NULL,
      `branch` varchar(150) DEFAULT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_entries`
      ADD PRIMARY KEY (`id`),
      ADD KEY `form_id` (`form_id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_entries`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->field_exists('message', db_prefix() . 'web_integration_entries')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_entries` ADD `message` TEXT DEFAULT NULL;');
}

if (!$CI->db->field_exists('rating', db_prefix() . 'web_integration_entries')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_entries` ADD `rating` INT(11) DEFAULT NULL;');
}

if (!$CI->db->field_exists('lead_source', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `lead_source` INT(11) DEFAULT 0;');
}

if (!$CI->db->field_exists('lead_assigned', db_prefix() . 'web_integration_forms')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'web_integration_forms` ADD `lead_assigned` INT(11) DEFAULT 0;');
}
