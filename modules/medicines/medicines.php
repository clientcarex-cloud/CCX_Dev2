<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Medicines
Description: Manage medicines inventory and details.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MEDICINES_MODULE_NAME', 'medicines');

hooks()->add_action('admin_init', 'medicines_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(MEDICINES_MODULE_NAME, 'medicines_module_activation_hook');

function medicines_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    // Check if Pharmacy item group exists
    $group_exists = $CI->db->where('name', 'Pharmacy')->get(db_prefix() . 'items_groups')->row();

    if (!$group_exists) {
        $CI->db->insert(db_prefix() . 'items_groups', [
            'name' => 'Pharmacy'
        ]);
        log_activity('Pharmacy item group created by Medicines module activation');
    }
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MEDICINES_MODULE_NAME, [MEDICINES_MODULE_NAME]);

/**
 * Init medicines module menu items in setup in admin_init hook
 * @return null
 */
function medicines_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('medicines', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('medicines', [
            'name' => _l('medicines'),
            'href' => admin_url('medicines'),
            'icon' => 'fa fa-medkit', // Suitable icon
            'position' => 30,
        ]);
    }
}
