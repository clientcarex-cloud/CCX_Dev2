<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'b2b_labs')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'b2b_labs` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'b2b_labs`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'b2b_labs`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

// Check if role "Referral Lab" exists
$CI->db->where('name', 'Referral Lab');
$role = $CI->db->get(db_prefix() . 'roles')->row();

if (!$role) {
  $CI->db->insert(db_prefix() . 'roles', [
    'name' => 'Referral Lab',
    'permissions' => serialize([]),
  ]);
}
