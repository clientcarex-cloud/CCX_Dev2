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
  `dateadded` datetime NOT NULL,
  `addedfrom` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
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
