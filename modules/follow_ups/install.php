<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'follow_ups')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'follow_ups` (
      `id` int(11) NOT NULL,
      `subject` varchar(191) NOT NULL,
      `description` text,
      `start_date` datetime NOT NULL,
      `end_date` datetime DEFAULT NULL,
      `staff_id` int(11) NOT NULL,
      `datecreated` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'follow_ups`
      ADD PRIMARY KEY (`id`),
      ADD KEY `staff_id` (`staff_id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'follow_ups`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
