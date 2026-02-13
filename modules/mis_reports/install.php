<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'mis_reports')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'mis_reports` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `type` varchar(255) NOT NULL,
      `description` text,
      `allowed_roles` text,
      `allowed_staff` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('allowed_roles', db_prefix() . 'mis_reports')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_roles` TEXT NULL AFTER `description`;');
}

if (!$CI->db->field_exists('allowed_staff', db_prefix() . 'mis_reports')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'mis_reports` ADD `allowed_staff` TEXT NULL AFTER `allowed_roles`;');
}
