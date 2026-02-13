<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'print_templates')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'print_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(100) NOT NULL,
  `content` longtext,
  `is_default` int(11) NOT NULL DEFAULT "0",
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'print_templates`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'print_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'print_template_types')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'print_template_types` (
  `id` int(11) NOT NULL,
  `type` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'print_template_types`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'print_template_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'print_template_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

$types = [
  'Invoice',
  'Bill',
  'Lab Test Report (Fixed)',
  'Lab Test Report (Word)',
  'PNDT',
  'Refund',
  'Barcode',
  'OP Bill',
  'Expense',
  'Pharmacy Bill',
  'Prescription',
  'IP Bill',
  'Refund Bill',
  'Report',
  'Discharge Summary',
  'IP Final Bill',
  'Report Header',
  'Report Footer',
  'Department Footer',
  'Department Header',
];

foreach ($types as $type) {
  if ($CI->db->table_exists(db_prefix() . 'print_template_types')) {
    $exists = $CI->db->where('type', $type)->get(db_prefix() . 'print_template_types')->row();
    if (!$exists) {
      $CI->db->insert(db_prefix() . 'print_template_types', ['type' => $type]);
    }
  }
}
