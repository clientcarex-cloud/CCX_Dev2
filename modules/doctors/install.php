<?php

defined('BASEPATH') or exit('No direct script access allowed');

$roles_to_create = [
    'Doctor',
    'Jr. Doctor',
    'Sr. Doctor',
];

foreach ($roles_to_create as $role_name) {
    if (total_rows(db_prefix() . 'roles', ['name' => $role_name]) == 0) {
        $CI->db->insert(db_prefix() . 'roles', [
            'name' => $role_name,
            'permissions' => serialize([]), // Default no permissions, admin can configure
        ]);
    }
}

if (!$CI->db->field_exists('default_service_item', db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `default_service_item` INT NULL DEFAULT NULL');
}

if (!$CI->db->field_exists('anytime_appointment', db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `anytime_appointment` INT DEFAULT 0');
}

if (!$CI->db->field_exists('specialization', db_prefix() . 'staff')) {
    // Add specialization column
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `specialization` VARCHAR(255) NULL DEFAULT NULL');
}

$new_fields = [
    'sex' => 'VARCHAR(50) NULL DEFAULT NULL',
    'experience' => 'VARCHAR(100) NULL DEFAULT NULL',
    'date_of_birth' => 'DATE NULL DEFAULT NULL',
    'area' => 'VARCHAR(255) NULL DEFAULT NULL',
    'qualification' => 'VARCHAR(255) NULL DEFAULT NULL',
    'designation' => 'VARCHAR(255) NULL DEFAULT NULL',
    'department' => 'VARCHAR(255) NULL DEFAULT NULL',
    'professional_id' => 'VARCHAR(100) NULL DEFAULT NULL',
    'doctor_profile_type' => 'VARCHAR(50) NULL DEFAULT NULL',
    'signature_image' => 'VARCHAR(255) NULL DEFAULT NULL',
];


foreach ($new_fields as $field => $definition) {
    if (!$CI->db->field_exists($field, db_prefix() . 'staff')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `' . $field . '` ' . $definition);
    }
}

