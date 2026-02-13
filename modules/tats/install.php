<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'patient_test_history')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'patient_test_history` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `test_id` int(11) NOT NULL,
      `status` varchar(100) NOT NULL,
      `staff_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `test_id` (`test_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
