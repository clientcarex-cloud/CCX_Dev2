<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {
    $CI->db->query('DROP TABLE `' . db_prefix() . 'patients_extra`');
}
