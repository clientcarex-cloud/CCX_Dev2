<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'shift_reports')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "shift_reports` (
      `id` int(11) NOT NULL,
      `staff_id` int(11) NOT NULL,
      `report_date` date NOT NULL,
      `content` text,
      `created_at` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'shift_reports`
      ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'shift_reports`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}
