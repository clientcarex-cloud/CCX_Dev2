<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'medicines')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT 0,
  `price` decimal(15,2) DEFAULT 0.00,
  `quantity` int(11) DEFAULT 0,
  `description` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'medicine_instructions')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_instructions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `instruction` text,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('type', db_prefix() . 'medicine_instructions')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_instructions` ADD `type` VARCHAR(255) DEFAULT NULL AFTER `instruction`;');
}
