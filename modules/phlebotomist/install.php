<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'phlebotomist')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'phlebotomist` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `patient_id` int(11) NOT NULL,
      `patient_test_id` int(11) NOT NULL DEFAULT 0,
      `test_name` varchar(255) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('sample_id', db_prefix() . 'phlebotomist')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'phlebotomist` ADD `sample_id` VARCHAR(50) DEFAULT NULL AFTER `id`;');
}
