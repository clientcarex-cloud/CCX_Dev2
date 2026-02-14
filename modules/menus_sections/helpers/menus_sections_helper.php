<?php

defined('BASEPATH') or exit('No direct script access allowed');

function menus_sections_inject($items)
{
    $CI = &get_instance();

    // Check if table exists
    if (!$CI->db->table_exists(db_prefix() . 'menus_sections')) {
        return $items;
    }

    $saved_rows = $CI->db->select('*')
        ->from(db_prefix() . 'menus_sections')
        ->order_by('position', 'ASC')
        ->get()
        ->result_array();

    // If no config, return original items
    if (empty($saved_rows)) {
        return $items;
    }

    $new_items = [];
    $position_counter = 5; // Start positions

    // Index original items by slug for quick retrieval
    $items_map = [];
    foreach ($items as $key => $item) {
        $items_map[$item['slug']] = $item;
    }

    foreach ($saved_rows as $node) {
        if ($node['type'] === 'section') {
            // Create a section item
            $section_slug = $node['slug'];

            $new_items[$section_slug] = [
                'slug' => $section_slug,
                'name' => $node['name'],
                'icon' => '',
                'href' => '#',
                'position' => $position_counter++,
                'li_attributes' => [
                    'class' => 'menu-section-label',
                    'id' => $section_slug,
                ],
                'children' => []
            ];

        } else if ($node['type'] === 'item') {
            $slug = $node['slug'];
            if (isset($items_map[$slug])) {
                $item = $items_map[$slug];
                $item['position'] = $position_counter++;
                $new_items[$slug] = $item;

                // Remove from map so we know what's left
                unset($items_map[$slug]);
            }
        }
    }

    // Append any remaining items that weren't in the saved config (newly added modules etc)
    foreach ($items_map as $slug => $item) {
        $item['position'] = $position_counter++;
        $new_items[$slug] = $item;
    }

    return $new_items;
}


