<?php

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'name_titles')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'name_titles` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'name_titles`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'name_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'name_care_titles')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'name_care_titles` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'name_care_titles`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'name_care_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

// Preset Name Titles
$name_titles = [
  'Mr.',
  'Mrs.',
  'Ms.',
  'Baby',
  'Master',
  'Dr.',
];

foreach ($name_titles as $name) {
  if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'name_titles') == 0) {
    $CI->db->insert(db_prefix() . 'name_titles', [
      'name' => $name,
    ]);
  }
}


// Preset Name Care Titles
$name_care_titles = [
  'W/O',
  'D/O',
  'S/O',
];

foreach ($name_care_titles as $name) {
  if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'name_care_titles') == 0) {
    $CI->db->insert(db_prefix() . 'name_care_titles', [
      'name' => $name,
    ]);
  }
}

if (!$CI->db->table_exists(db_prefix() . 'lab_tests_statuses')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'lab_tests_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'lab_tests_statuses`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'lab_tests_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

// Preset Lab Tests Statuses
// Preset Lab Tests Statuses
$lab_tests_statuses = [
  'Regular',
  'Emergency',
  'Pending',
  'Sample Collected',
  'Sample Lost',
  'Sample Insufficient',
  'Processing',
  'Authorization Required',
  'Authorized',
  'Completed',
  'Cancelled',
];

// Check if we need to re-seed (e.g. if 'Sample Collected' is missing or order is wrong)
// We check if the count matches and if a specific new item exists.
// For simplicity in this dev/setup phase, we can enforce the list if the counts mismatch or if we want to be strict.
// A safe check: if the count of rows is different than our list, OR if 'Sample Collected' (new item) is likely missing.
$force_update = false;
if ($CI->db->count_all(db_prefix() . 'lab_tests_statuses') != count($lab_tests_statuses)) {
  $force_update = true;
} else {
  // Check if ID 4 is 'Sample Collected' (as per requested order)
  $check_id_4 = $CI->db->where('id', 4)->get(db_prefix() . 'lab_tests_statuses')->row();
  if (!$check_id_4 || $check_id_4->name !== 'Sample Collected') {
    $force_update = true;
  }
}

if ($force_update) {
  // TRUNCATE to reset IDs
  $CI->db->query('TRUNCATE TABLE `' . db_prefix() . 'lab_tests_statuses`');

  foreach ($lab_tests_statuses as $name) {
    $CI->db->insert(db_prefix() . 'lab_tests_statuses', [
      'name' => $name,
    ]);
  }
} else {
  // Fallback: Ensure they exist (standard check, though the above covers most cases)
  foreach ($lab_tests_statuses as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'lab_tests_statuses') == 0) {
      $CI->db->insert(db_prefix() . 'lab_tests_statuses', [
        'name' => $name,
      ]);
    }
  }
}

if (!$CI->db->table_exists(db_prefix() . 'medicine_types')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_types` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_types`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  // Preset Medicine Types
  $medicine_types = [
    'Tablet',
    'Capsule',
    'Syrup',
    'Injection',
  ];

  foreach ($medicine_types as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'medicine_types') == 0) {
      $CI->db->insert(db_prefix() . 'medicine_types', [
        'name' => $name,
      ]);
    }
  }
}

// Medicine Doses
if (!$CI->db->table_exists(db_prefix() . 'medicine_doses')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_doses` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_doses`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_doses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  $doses = ['5ml', '10ml', '1 Tablet', '2 Tablets', '1 Capsule', '1 Injection'];
  foreach ($doses as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'medicine_doses') == 0) {
      $CI->db->insert(db_prefix() . 'medicine_doses', ['name' => $name]);
    }
  }
}

// Medicine Frequency
if (!$CI->db->table_exists(db_prefix() . 'medicine_frequencies')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_frequencies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_frequencies`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_frequencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  $frequencies = ['1-0-1', '1-1-1', '0-0-1', '1-0-0', 'Once a day', 'Twice a day'];
  foreach ($frequencies as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'medicine_frequencies') == 0) {
      $CI->db->insert(db_prefix() . 'medicine_frequencies', ['name' => $name]);
    }
  }
}

// Medicine Duration
if (!$CI->db->table_exists(db_prefix() . 'medicine_durations')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_durations` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_durations`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_durations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  $durations = ['3 Days', '5 Days', '7 Days', '1 Week', '2 Weeks', '1 Month'];
  foreach ($durations as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'medicine_durations') == 0) {
      $CI->db->insert(db_prefix() . 'medicine_durations', ['name' => $name]);
    }
  }
}

// Medicine When
if (!$CI->db->table_exists(db_prefix() . 'medicine_whens')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'medicine_whens` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_whens`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'medicine_whens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

  $whens = ['After Food', 'Before Food', 'With Food', 'Empty Stomach'];
  foreach ($whens as $name) {
    if ($CI->db->where('name', $name)->count_all_results(db_prefix() . 'medicine_whens') == 0) {
      $CI->db->insert(db_prefix() . 'medicine_whens', ['name' => $name]);
    }
  }
}

// Treatments
if (!$CI->db->table_exists(db_prefix() . 'treatments')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'treatments` (
    `id` int(11) NOT NULL,
    `name` varchar(150) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'treatments`
    ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'treatments`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

// Department Groups
if (!$CI->db->table_exists(db_prefix() . 'department_groups')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'department_groups` (
    `id` int(11) NOT NULL,
    `name` varchar(150) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'department_groups`
    ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'department_groups`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

// Department Group Items (Pivot table for Many-to-Many relationship)
if (!$CI->db->table_exists(db_prefix() . 'department_group_items')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'department_group_items` (
    `id` int(11) NOT NULL,
    `group_id` int(11) NOT NULL,
    `department_id` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'department_group_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `group_id` (`group_id`),
    ADD KEY `department_id` (`department_id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'department_group_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

