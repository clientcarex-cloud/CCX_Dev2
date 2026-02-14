<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'menus_sections')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'menus_sections` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `slug` varchar(150) NOT NULL,
      `name` varchar(150) DEFAULT NULL,
      `type` varchar(20) NOT NULL,
      `position` int(11) DEFAULT 0,
      `parent_slug` varchar(150) DEFAULT NULL,
      `options` text DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Migration logic
if (get_option('menus_sections_active')) {
    $current_config = json_decode(get_option('menus_sections_active'), true);
    if (is_array($current_config) && !empty($current_config)) {
        // Check if table is empty to avoid double migration
        if ($CI->db->count_all(db_prefix() . 'menus_sections') == 0) {
            $position = 1;
            foreach ($current_config as $item) {
                // Determine layout
                // The old JSON structure was likely a flat list of objects.
                // We fit it into the new schema.

                $slug = isset($item['id']) ? $item['id'] : (isset($item['slug']) ? $item['slug'] : '');
                if (empty($slug))
                    continue;

                $type = isset($item['type']) ? $item['type'] : 'item';
                $name = isset($item['name']) ? $item['name'] : '';

                $CI->db->insert(db_prefix() . 'menus_sections', [
                    'slug' => $slug,
                    'name' => $name,
                    'type' => $type,
                    'position' => $position,
                    'options' => json_encode($item)
                ]);
                $position++;
            }
        }
    }
    // Ideally we would delete the option, but let's keep it for safety for now or delete it
    // delete_option('menus_sections_active'); 
}
