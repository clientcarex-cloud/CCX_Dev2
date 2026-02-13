<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'discharge_summaries')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'discharge_summaries` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `visit_id` int(11) NOT NULL,
      `patient_id` int(11) NOT NULL,
      `summary_content` longtext,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `visit_id` (`visit_id`),
      KEY `patient_id` (`patient_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ip_discharge')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'ip_discharge` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'ip_discharge`
      ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'ip_discharge`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
