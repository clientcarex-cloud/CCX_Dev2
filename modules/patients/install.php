<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'patients_extra')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'patients_extra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `title_id` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `referral_doctor_id` int(11) DEFAULT NULL,
  `attender_title_id` int(11) DEFAULT NULL,
  `attender_name` varchar(150) DEFAULT NULL,
  `referral_lab_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `prescription_file` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'patient_tests')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'patient_tests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `invoice_id` int(11) DEFAULT NULL,
    `item_id` int(11) NOT NULL,
    `status` varchar(50) DEFAULT "Processing",
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

$roles_to_create = ['Company', 'Referral Lab'];
foreach ($roles_to_create as $role) {
  if (total_rows(db_prefix() . 'roles', ['name' => $role]) == 0) {
    $CI->db->insert(db_prefix() . 'roles', [
      'name' => $role,
      'permissions' => serialize([]),
    ]);
  }
}

// Proceed with field checks
if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {

  if (!$CI->db->field_exists('title_id', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `title_id` INT(11) DEFAULT NULL AFTER `patient_id`');
  }
  if (!$CI->db->field_exists('gender', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `gender` VARCHAR(20) DEFAULT NULL AFTER `title_id`');
  }
  if (!$CI->db->field_exists('referral_doctor_id', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `referral_doctor_id` INT(11) DEFAULT NULL AFTER `mobile_number`');
  }
  if (!$CI->db->field_exists('attender_title_id', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `attender_title_id` INT(11) DEFAULT NULL AFTER `referral_doctor_id`');
  }
  if (!$CI->db->field_exists('attender_name', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `attender_name` VARCHAR(150) DEFAULT NULL AFTER `attender_title_id`');
  }
  if (!$CI->db->field_exists('referral_lab_id', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `referral_lab_id` INT(11) DEFAULT NULL AFTER `attender_name`');
  }
  if (!$CI->db->field_exists('company_id', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `company_id` INT(11) DEFAULT NULL AFTER `referral_lab_id`');
  }
  if (!$CI->db->field_exists('prescription_file', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `prescription_file` VARCHAR(255) DEFAULT NULL AFTER `company_id`');
  }
  if (!$CI->db->field_exists('mr_number', db_prefix() . 'patients_extra')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'patients_extra` ADD `mr_number` VARCHAR(50) DEFAULT NULL AFTER `id`');
  }
}

// Create tblvisits table
if (!$CI->db->table_exists(db_prefix() . 'visits')) {
  $CI->db->query("CREATE TABLE `" . db_prefix() . "visits` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `patient_id` int(11) NOT NULL,
        `invoice_id` int(11) NOT NULL,
        `visit_code` varchar(50) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
}

// Create Default Item Groups
$groups_to_create = ['Fee', 'Tests', 'Services', 'Rooms', 'Packages'];
foreach ($groups_to_create as $group_name) {
  if (total_rows(db_prefix() . 'items_groups', ['name' => $group_name]) == 0) {
    $CI->db->insert(db_prefix() . 'items_groups', [
      'name' => $group_name,
    ]);
  }
}
// Auto-migration for schema updates (Moved from Patients_model)
if ($CI->db->table_exists(db_prefix() . 'visits')) {
  if (!$CI->db->field_exists('referral_doctor_id', db_prefix() . 'visits')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "visits ADD COLUMN referral_doctor_id INT NULL");
  }
  if (!$CI->db->field_exists('referral_lab_id', db_prefix() . 'visits')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "visits ADD COLUMN referral_lab_id INT NULL");
  }
  if (!$CI->db->field_exists('company_id', db_prefix() . 'visits')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "visits ADD COLUMN company_id INT NULL");
  }
  if (!$CI->db->field_exists('primary_doctor_id', db_prefix() . 'visits')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "visits ADD COLUMN primary_doctor_id INT NULL");
  }
}

if ($CI->db->table_exists(db_prefix() . 'patient_tests')) {
  if (!$CI->db->field_exists('is_emergency', db_prefix() . 'patient_tests')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "patient_tests ADD COLUMN is_emergency TINYINT(1) DEFAULT 0");
  }
}

if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {
  if (!$CI->db->field_exists('dob', db_prefix() . 'patients_extra')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "patients_extra ADD COLUMN dob DATE NULL");
  }
  if (!$CI->db->field_exists('age_unit', db_prefix() . 'patients_extra')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "patients_extra ADD COLUMN age_unit VARCHAR(20) DEFAULT 'Years'");
  }
  if (!$CI->db->field_exists('uid_no', db_prefix() . 'patients_extra')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "patients_extra ADD COLUMN uid_no VARCHAR(50) NULL");
  }
}

// Add Indexes for Performance
if ($CI->db->table_exists(db_prefix() . 'patients_extra')) {
  $table = db_prefix() . 'patients_extra';
  // Index for patient_id (Foreign Key)
  $index_params = ['patient_id'];
  // Check if index exists usually requires checking information_schema, but CI3 doesn't have a simple valid_index check.
  // We can try to add it and catch error, or more safely, use a helper or raw SQL query to check.
  // Standard Perfex pattern often just runs the ALTER IGNORE or checks via query.

  $indexes = [
    'idx_patients_extra_patient_id' => 'patient_id',
    'idx_patients_extra_mobile' => 'mobile_number',
    'idx_patients_extra_mr_number' => 'mr_number'
  ];

  foreach ($indexes as $key => $column) {
    // Simple check: does show index return it?
    $exist = $CI->db->query("SHOW INDEX FROM " . $table . " WHERE Key_name = '" . $key . "'")->row();
    if (!$exist) {
      $CI->db->query("ALTER TABLE " . $table . " ADD INDEX " . $key . " (" . $column . ")");
    }
  }
}

if ($CI->db->table_exists(db_prefix() . 'visits')) {
  $table = db_prefix() . 'visits';
  $indexes = [
    'idx_visits_patient_id' => 'patient_id',
    'idx_visits_invoice_id' => 'invoice_id',
    'idx_visits_visit_code' => 'visit_code',
    'idx_visits_created_at' => 'created_at'
  ];

  foreach ($indexes as $key => $column) {
    $exist = $CI->db->query("SHOW INDEX FROM " . $table . " WHERE Key_name = '" . $key . "'")->row();
    if (!$exist) {
      $CI->db->query("ALTER TABLE " . $table . " ADD INDEX " . $key . " (" . $column . ")");
    }
  }
}

if ($CI->db->table_exists(db_prefix() . 'patient_tests')) {
  $table = db_prefix() . 'patient_tests';
  $indexes = [
    'idx_patient_tests_invoice_id' => 'invoice_id',
    'idx_patient_tests_patient_id' => 'patient_id',
    'idx_patient_tests_item_id' => 'item_id'
  ];
  foreach ($indexes as $key => $column) {
    $exist = $CI->db->query("SHOW INDEX FROM " . $table . " WHERE Key_name = '" . $key . "'")->row();
    if (!$exist) {
      $CI->db->query("ALTER TABLE " . $table . " ADD INDEX " . $key . " (" . $column . ")");
    }
  }
}
