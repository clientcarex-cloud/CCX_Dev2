<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->field_exists('is_pt_subscription', db_prefix() . 'invoices')) {
  $CI->db->query("ALTER TABLE `" . db_prefix() . "invoices` ADD `is_pt_subscription` INT(1) DEFAULT 0;");
}
if (!$CI->db->field_exists('pt_subscription_status', db_prefix() . 'invoices')) {
  $CI->db->query("ALTER TABLE `" . db_prefix() . "invoices` ADD `pt_subscription_status` VARCHAR(50) DEFAULT 'active';");
}
