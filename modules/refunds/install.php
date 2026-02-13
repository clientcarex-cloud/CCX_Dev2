<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'refunds')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'refunds` (
      `id` int(11) NOT NULL,
      `invoice_id` int(11) NOT NULL,
      `amount` decimal(15,2) NOT NULL,
      `refunded_on` date NOT NULL,
      `payment_mode` varchar(50) DEFAULT NULL,
      `note` text,
      `refund_type` varchar(50) DEFAULT NULL,
      `created_at` datetime NOT NULL,
      `created_by` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'refunds`
      ADD PRIMARY KEY (`id`),
      ADD KEY `invoice_id` (`invoice_id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'refunds`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
} else {
  // Check if refund_type column exists
  if (!$CI->db->field_exists('refund_type', db_prefix() . 'refunds')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'refunds` ADD `refund_type` VARCHAR(50) DEFAULT NULL AFTER `note`;');
  }
}

if (!$CI->db->table_exists(db_prefix() . 'refund_items')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'refund_items` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `refund_id` int(11) NOT NULL,
      `patient_test_id` int(11) NOT NULL,
      `refunded_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
      PRIMARY KEY (`id`),
      KEY `refund_id` (`refund_id`),
      KEY `patient_test_id` (`patient_test_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
