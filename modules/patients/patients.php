<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Patients
Description: Custom module for patients management
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PATIENTS_MODULE_NAME', 'patients');

hooks()->add_action('admin_init', 'patients_module_init_menu_items');

// Filter sidebar to remove Customers menu item
hooks()->add_filter('sidebar_menu_items', 'patients_module_remove_customers_menu');

// Register Permissions
hooks()->add_filter('staff_permissions', 'patients_module_permissions');

function patients_module_permissions($permissions)
{
    $permissions['patients'] = [
        'name' => 'Medical Records',
        'capabilities' => [
            'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit' => _l('permission_edit'),
            'delete' => _l('permission_delete'),
            'refunds' => 'Refunds',
            'history' => 'History'
        ]
    ];

    $permissions['visits'] = [
        'name' => 'Visits (Billing)',
        'capabilities' => [
            'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit' => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ]
    ];

    return $permissions;
}

function patients_module_remove_customers_menu($items)
{
    foreach ($items as $key => $item) {
        if (in_array($item['slug'], ['customers', 'customer', 'clients'])) {
            unset($items[$key]);
        }
    }
    return $items;
}

/**
 * Register activation module hook
 */
register_activation_hook(PATIENTS_MODULE_NAME, 'patients_module_activation_hook');

function patients_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PATIENTS_MODULE_NAME, [PATIENTS_MODULE_NAME]);

/**
 * Init patients module menu items in setup in admin_init hook
 * @return null
 */
function patients_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('patients', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('patients', [
            'name' => 'Medical Records',
            'href' => admin_url('patients'),
            'position' => 3, // Position near top
            'icon' => 'fa fa-user-injured', // Appropriate icon
        ]);
    }

    if (has_permission('visits', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('visits', [
            'name' => 'Billing',
            'href' => admin_url('patients/visits'),
            'position' => 4, // Below Patients
            'icon' => 'fa fa-file-invoice', // Updated icon (Generic Invoice)
        ]);
    }
}
