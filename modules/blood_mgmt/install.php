<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'blood_donors')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'blood_donors` (
      `id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `age` int(11) NOT NULL,
      `gender` varchar(20) NOT NULL,
      `blood_group` varchar(10) NOT NULL,
      `mobile` varchar(20) NOT NULL,
      `email` varchar(100) DEFAULT NULL,
      `last_donation_date` date DEFAULT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_donors` ADD PRIMARY KEY (`id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_donors` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'blood_bags')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'blood_bags` (
      `id` int(11) NOT NULL,
      `blood_group` varchar(10) NOT NULL,
      `donor_id` int(11) DEFAULT NULL,
      `donation_date` date NOT NULL,
      `expiry_date` date NOT NULL,
      `volume` varchar(50) DEFAULT NULL,
      `status` int(11) NOT NULL DEFAULT 1 COMMENT "1: Available, 2: Issued, 3: Expired, 4: Discarded",
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_bags` ADD PRIMARY KEY (`id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_bags` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'blood_issues')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'blood_issues` (
      `id` int(11) NOT NULL,
      `patient_name` varchar(150) NOT NULL,
      `doctor_name` varchar(150) DEFAULT NULL,
      `issue_date` datetime NOT NULL,
      `hospital` varchar(150) DEFAULT NULL,
      `amount` decimal(15,2) DEFAULT 0.00,
      `remarks` text DEFAULT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_issues` ADD PRIMARY KEY (`id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_issues` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'blood_issue_items')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'blood_issue_items` (
      `id` int(11) NOT NULL,
      `issue_id` int(11) NOT NULL,
      `bag_id` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_issue_items` ADD PRIMARY KEY (`id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'blood_issue_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
