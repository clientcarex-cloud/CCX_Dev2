<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'case_taking')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'case_taking` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `patient_id` int(11) NOT NULL,
                `visit_id` int(11) NOT NULL,
                `staff_id` int(11) NOT NULL,
                `datecreated` datetime NOT NULL,
                `bp_sys` varchar(10) DEFAULT NULL,
                `bp_dia` varchar(10) DEFAULT NULL,
                `pulse` varchar(10) DEFAULT NULL,
                `height` varchar(10) DEFAULT NULL,
                `weight` varchar(10) DEFAULT NULL,
                `temperature` varchar(10) DEFAULT NULL,
                `spo2` varchar(10) DEFAULT NULL,
                `bmi` varchar(10) DEFAULT NULL,
                `complaints` text,
                `diagnosis` text,
                `advice` text,
                `personal_history` text,
                `family_history` text,
                `allergies` text,
                `vaccination` text,
                `tests_requested` text,
                `next_visit_qty` int(11) DEFAULT NULL,
                `next_visit_unit` varchar(20) DEFAULT NULL,
                `next_visit_date` date DEFAULT NULL,
                `date_completed` datetime DEFAULT NULL,
                `consultation_duration` int(11) DEFAULT 0,
                `custom_data` LONGTEXT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'case_taking_qa_templates')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'case_taking_qa_templates` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `questions` text,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'case_taking_items')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'case_taking_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `case_taking_id` int(11) NOT NULL,
                `type` varchar(50) DEFAULT NULL,
                `medicine_name` varchar(255) DEFAULT NULL,
                `dose` varchar(50) DEFAULT NULL,
                `when_f` varchar(50) DEFAULT NULL,
                `frequency` varchar(50) DEFAULT NULL,
                `duration` varchar(50) DEFAULT NULL,
                `instruction` text,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'case_taking_master_data')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'case_taking_master_data` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `category` varchar(100) NOT NULL,
              `name` varchar(255) NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// New Custom Fields Table
if (!$CI->db->table_exists(db_prefix() . 'case_taking_custom_fields')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'case_taking_custom_fields` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `slug` varchar(100) NOT NULL,
                `label` varchar(255) NOT NULL,
                `type` varchar(50) DEFAULT "smart_editor",
                `field_order` int(11) DEFAULT 0,
                `mandatory` tinyint(1) DEFAULT 0,
                `is_active` tinyint(1) DEFAULT 1,
                `is_system` tinyint(1) DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  // Populate Default System Fields
  $default_fields = [
    ['slug' => 'complaints', 'label' => 'Complaints', 'field_order' => 1],
    ['slug' => 'diagnosis', 'label' => 'Diagnosis', 'field_order' => 2],
    ['slug' => 'advice', 'label' => 'Advice', 'field_order' => 3],
    ['slug' => 'personal_history', 'label' => 'Personal History', 'field_order' => 4],
    ['slug' => 'family_history', 'label' => 'Family History', 'field_order' => 5],
    ['slug' => 'allergies', 'label' => 'Allergies', 'field_order' => 6],
    ['slug' => 'vaccination', 'label' => 'Vaccination', 'field_order' => 7]
  ];

  foreach ($default_fields as $field) {
    $CI->db->insert(db_prefix() . 'case_taking_custom_fields', [
      'slug' => $field['slug'],
      'label' => $field['label'],
      'type' => get_option('case_taking_input_type_' . $field['slug']) ? get_option('case_taking_input_type_' . $field['slug']) : 'smart_editor',
      'field_order' => $field['field_order'],
      'mandatory' => get_option('case_taking_mandatory_' . $field['slug']),
      'is_active' => get_option('case_taking_show_' . $field['slug']),
      'is_system' => 1
    ]);
  }
}
