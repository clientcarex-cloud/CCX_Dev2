<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'self_kiosk_qrcodes')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'self_kiosk_qrcodes` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `description` text,
      `slug` varchar(150) NOT NULL,
      `settings` text,
      `date_created` datetime NOT NULL,
      `staff_id` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'self_kiosk_qrcodes`
      ADD PRIMARY KEY (`id`),
      ADD UNIQUE KEY `slug` (`slug`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'self_kiosk_qrcodes`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
