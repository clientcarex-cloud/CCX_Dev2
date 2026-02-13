<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Table: tblbuildings
if (!$CI->db->table_exists(db_prefix() . 'buildings')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'buildings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(191) NOT NULL,
      `description` text,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Table: tblfloors
if (!$CI->db->table_exists(db_prefix() . 'floors')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'floors` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `building_id` int(11) NOT NULL,
      `name` varchar(191) NOT NULL,
      `floor_level` int(11) NOT NULL DEFAULT 0,
      `description` text,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Table: tblroom_details (Mapping between Floors and Items)
if (!$CI->db->table_exists(db_prefix() . 'room_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'room_details` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `item_id` int(11) NOT NULL,
      `floor_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Clean up old table if it exists and differs (optional, but good for clean slate if previous runs failed or were different)
// In this case, I see a `rooms` table in the original `install.php` that seems unused/conflicting with my new plan. 
// I will keep it for now but my plan uses `tblroom_details` + `tblitems`. 
// The prompt said: "rooms means perfex crm item only with item group name 'Rooms'".

// Ensure 'Rooms' item group exists
$CI->load->model('invoice_items_model');
$rooms_group = $CI->db->where('name', 'Rooms')->get(db_prefix() . 'items_groups')->row();

if (!$rooms_group) {
  $CI->db->insert(db_prefix() . 'items_groups', [
    'name' => 'Rooms',
  ]);
}
