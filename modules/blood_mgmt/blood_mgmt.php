<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Blood Mgmt
Description: Module for Blood Mgmt.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('BLOOD_MGMT_MODULE_NAME', 'blood_mgmt');

hooks()->add_action('admin_init', 'blood_mgmt_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(BLOOD_MGMT_MODULE_NAME, 'blood_mgmt_module_activation_hook');

function blood_mgmt_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(BLOOD_MGMT_MODULE_NAME, [BLOOD_MGMT_MODULE_NAME]);

/**
 * Init blood_mgmt module menu items in setup in admin_init hook
 * @return null
 */
function blood_mgmt_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('blood_mgmt', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('blood_mgmt', [
            'name' => 'Blood Mgmt', // Directly using name for now, usually _l()
            'href' => admin_url('blood_mgmt'),
            'icon' => 'fa fa-tint',
            'position' => 30,
        ]);
    }
}

hooks()->add_action('admin_init', 'blood_mgmt_permissions');

function blood_mgmt_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('blood_mgmt', $capabilities, _l('blood_mgmt'));
}
