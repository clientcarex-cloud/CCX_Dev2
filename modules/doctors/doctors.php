<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Doctors
Description: Manage Doctors, Jr. Doctors, and Sr. Doctors
Version: 1.0.0
Requires at least: 2.3.*
*/

define('DOCTORS_MODULE_NAME', 'doctors');

hooks()->add_action('admin_init', 'doctors_module_init_menu_items');
hooks()->add_action('admin_init', 'doctors_permissions');

/**
 * Register activation module hook
 */
register_activation_hook(DOCTORS_MODULE_NAME, 'doctors_module_activation_hook');

function doctors_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(DOCTORS_MODULE_NAME, [DOCTORS_MODULE_NAME]);

/**
 * Init doctors module menu items in setup in admin_init hook
 * @return null
 */
function doctors_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('doctors', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('doctors', [
            'name' => 'Doctors',
            'href' => admin_url('doctors'),
            'icon' => 'fa fa-user-md',
            'position' => 6,
        ]);

        $CI->app_menu->add_sidebar_children_item('doctors', [
            'slug' => 'doctors-list',
            'name' => 'Manage',
            'href' => admin_url('doctors'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('doctors', [
            'slug' => 'doctors-roles',
            'name' => 'Doctor Roles',
            'href' => admin_url('doctors/roles'),
            'position' => 10,
        ]);


    }
}

function doctors_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('doctors', $capabilities, 'Doctors');
}
