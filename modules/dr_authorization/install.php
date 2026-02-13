<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'dr_authorization')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'dr_authorization` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'dr_authorization`
      ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'dr_authorization`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
