<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'ccx_leads')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'ccx_leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `company` varchar(191) DEFAULT NULL,
  `phonenumber` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `source` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `assigned` int(11) DEFAULT 0,
  `lead_value` decimal(15,2) DEFAULT NULL,
  `priority` int(11) DEFAULT 0,
  `dateadded` datetime NOT NULL,
  `addedfrom` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` int(11) DEFAULT 0,
  `zip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

/* Add columns if they don't exist (for updates) */
$columns = [
  'title' => 'VARCHAR(100) NULL DEFAULT NULL',
  'website' => 'VARCHAR(150) NULL DEFAULT NULL',
  'description' => 'TEXT NULL DEFAULT NULL',
  'address' => 'VARCHAR(100) NULL DEFAULT NULL',
  'city' => 'VARCHAR(100) NULL DEFAULT NULL',
  'state' => 'VARCHAR(100) NULL DEFAULT NULL',
  'country' => 'INT(11) DEFAULT 0',
  'lead_value' => 'DECIMAL(15,2) DEFAULT NULL',
  'priority' => 'INT(11) DEFAULT 0',
  'zip' => 'VARCHAR(15) NULL DEFAULT NULL',
];

foreach ($columns as $column => $definition) {
  if (!$CI->db->field_exists($column, db_prefix() . 'ccx_leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "ccx_leads` ADD `" . $column . "` " . $definition);
  }
}

if (!$CI->db->table_exists(db_prefix() . 'ccx_leads_call_logs')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'ccx_leads_call_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `content` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ccx_leads_custom_fields')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'ccx_leads_custom_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(150) NOT NULL,
    `slug` varchar(150) NOT NULL,
    `type` varchar(50) NOT NULL,
    `options` text DEFAULT NULL,
    `mandatory` int(11) DEFAULT 0,
    `status` int(11) DEFAULT 1,
    `field_order` int(11) DEFAULT 0,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ccx_leads_custom_values')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'ccx_leads_custom_values` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `lead_id` int(11) NOT NULL,
    `field_id` int(11) NOT NULL,
    `value` text DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
