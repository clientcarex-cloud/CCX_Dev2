<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create Patient Guests Table (Shared/Moved ownership)
$CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . "patient_guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `phone_idx` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

if (!$CI->db->field_exists('title', db_prefix() . 'patient_guests')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'patient_guests` ADD `title` VARCHAR(20) DEFAULT NULL AFTER `id`');
}

// Create Appointments Table
$CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . "appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL, -- Nullable for new/guest patients
  `guest_id` int(11) DEFAULT NULL, -- Link to guest table
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending', -- pending, confirmed, cancelled, completed, visited
  `appointment_type` varchar(50) DEFAULT 'Unpaid', -- Paid, Unpaid, Free Camp
  `visit_confirmed_at` datetime DEFAULT NULL,
  `notes` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `patient_idx` (`patient_id`),
  INDEX `guest_idx` (`guest_id`),
  INDEX `doctor_idx` (`doctor_id`),
  INDEX `date_idx` (`appointment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

// Add columns if table exists but columns don't (for updates)
if (!$CI->db->field_exists('guest_id', db_prefix() . 'appointments')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointments` ADD `guest_id` INT(11) DEFAULT NULL');
}
if (!$CI->db->field_exists('appointment_type', db_prefix() . 'appointments')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointments` ADD `appointment_type` VARCHAR(50) DEFAULT "Unpaid"');
}
if (!$CI->db->field_exists('visit_confirmed_at', db_prefix() . 'appointments')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointments` ADD `visit_confirmed_at` DATETIME DEFAULT NULL');
}

// Create Doctor Schedule Table
$CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . "doctor_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` varchar(20) NOT NULL, -- Monday, Tuesday, etc.
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` int(11) NOT NULL DEFAULT 30, -- in minutes
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `doctor_idx` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

// Create Appointment Reschedules Table
$CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . "appointment_reschedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `prev_date` date NOT NULL,
  `prev_start_time` time NOT NULL,
  `prev_end_time` time NOT NULL,
  `new_date` date NOT NULL,
  `new_start_time` time NOT NULL,
  `new_end_time` time NOT NULL,
  `rescheduled_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `appointment_idx` (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

if (get_option('appointments_buffer_time') === null) {
  add_option('appointments_buffer_time', 15);
}
