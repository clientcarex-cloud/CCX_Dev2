<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'privilege_card_types')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'privilege_card_types` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `price` decimal(15,2) NOT NULL DEFAULT "0.00",
      `validity_years` int(11) NOT NULL DEFAULT "1",
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'privilege_card_types`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'privilege_card_types`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'privilege_card_members')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'privilege_card_members` (
      `id` int(11) NOT NULL,
      `card_number` varchar(50) NOT NULL,
      `patient_id` int(11) NOT NULL,
      `card_type_id` int(11) NOT NULL,
      `issue_date` date NOT NULL,
      `expiry_date` date NOT NULL,
      `status` varchar(20) NOT NULL DEFAULT "active",
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'privilege_card_members`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'privilege_card_members`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
