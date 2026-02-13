<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'prescriptions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'prescriptions` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text,
  `datecreated` datetime NOT NULL,
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
