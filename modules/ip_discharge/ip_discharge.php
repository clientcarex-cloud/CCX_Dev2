<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: IP Discharge
Description: Module for IP Discharge.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('IP_DISCHARGE_MODULE_NAME', 'ip_discharge');

hooks()->add_action('admin_init', 'ip_discharge_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(IP_DISCHARGE_MODULE_NAME, 'ip_discharge_module_activation_hook');

function ip_discharge_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(IP_DISCHARGE_MODULE_NAME, [IP_DISCHARGE_MODULE_NAME]);

/**
 * Init ip_discharge module menu items in setup in admin_init hook
 * @return null
 */
function ip_discharge_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('ip_discharge', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('ip_discharge', [
            'name' => 'IP Discharge', // Directly using name for now, usually _l()
            'href' => admin_url('ip_discharge'),
            'icon' => 'fa fa-sign-out',
            'position' => 30,
        ]);
    }
}
