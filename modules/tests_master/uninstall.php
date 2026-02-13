<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Optional: Drop the column if you want to clean up completely on uninstall
// For now, we keep it to prevent data loss if uninstalled accidentally
// if ($CI->db->field_exists('code', db_prefix() . 'items')) {
//     $CI->db->query('ALTER TABLE `' . db_prefix() . 'items` DROP COLUMN `code`');
// }
