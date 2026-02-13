<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Appointments
Description: Manage appointments for patients and guests.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('APPOINTMENTS_MODULE_NAME', 'appointments');

hooks()->add_action('admin_init', 'appointments_module_init_menu_items');
hooks()->add_action('admin_init', 'appointments_permissions');

/**
 * Register activation module hook
 */
register_activation_hook(APPOINTMENTS_MODULE_NAME, 'appointments_module_activation_hook');
hooks()->add_action('after_cron_run', 'appointments_cron_hook');

function appointments_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function appointments_cron_hook()
{
    $CI = &get_instance();
    $CI->load->model('appointments/appointments_model');
    $CI->appointments_model->mark_missed_appointments();
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(APPOINTMENTS_MODULE_NAME, [APPOINTMENTS_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function appointments_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('appointments', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('appointments', [
            'name' => _l('appointments'), // _l('appointments')
            'href' => admin_url('appointments'),
            'icon' => 'fa fa-calendar',
            'position' => 35,
        ]);

        /*
        $CI->app_menu->add_sidebar_children_item('appointments', [
            'slug' => 'appointments-manage',
            'name' => _l('appointments_manage'),
            'href' => admin_url('appointments'),
            'position' => 25,
        ]);

        if (has_permission('appointments', '', 'edit')) {
            $CI->app_menu->add_sidebar_children_item('appointments', [
                'slug' => 'appointments-settings',
                'name' => _l('appointments_settings'),
                'href' => admin_url('appointments/settings'),
                'position' => 30,
            ]);
        }
        */
    }
}

function appointments_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('appointments', $capabilities, 'Appointments');
}
