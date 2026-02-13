<?php

defined('BASEPATH') or exit('No direct script access allowed');

function menus_sections_inject($items)
{
    $saved_config = get_option('menus_sections_active');

    // If no config, return original items
    if (!$saved_config || $saved_config === '[]') {
        return $items;
    }

    $saved_config = json_decode($saved_config, true);
    if (!is_array($saved_config)) {
        return $items;
    }

    $new_items = [];
    $position_counter = 5; // Start positions

    // Index original items by slug for quick retrieval
    $items_map = [];
    foreach ($items as $key => $item) {
        $items_map[$item['slug']] = $item;
    }

    foreach ($saved_config as $node) {
        if ($node['type'] === 'section') {
            // Create a section item
            // We use a dummy slug but with a unique ID
            $section_slug = $node['id'];

            $new_items[$section_slug] = [
                'slug' => $section_slug,
                'name' => $node['name'], // Name is the section title - Arrow added via CSS
                'icon' => '',
                'href' => '#',
                'position' => $position_counter++,
                'li_attributes' => [
                    'class' => 'menu-section-label',
                    'id' => $section_slug, // Add ID for JS targeting
                ],
                'children' => []
            ];

        } else if ($node['type'] === 'item') {
            $slug = $node['id'];
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
