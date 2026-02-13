<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Nursing Station
Description: Module for Nursing Station.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('NURSING_STATION_MODULE_NAME', 'nursing_station');

hooks()->add_action('admin_init', 'nursing_station_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(NURSING_STATION_MODULE_NAME, 'nursing_station_module_activation_hook');

function nursing_station_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(NURSING_STATION_MODULE_NAME, [NURSING_STATION_MODULE_NAME]);

/**
 * Init nursing_station module menu items in setup in admin_init hook
 * @return null
 */
function nursing_station_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('nursing_station', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('nursing_station', [
            'name' => 'Nursing Station', // Directly using name for now, usually _l()
            'href' => admin_url('nursing_station'),
            'icon' => 'fa fa-stethoscope',
            'position' => 30,
        ]);
    }
}
