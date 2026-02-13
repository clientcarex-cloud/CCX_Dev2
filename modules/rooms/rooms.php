<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Rooms
Description: Module for Rooms.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('ROOMS_MODULE_NAME', 'rooms');

hooks()->add_action('admin_init', 'rooms_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(ROOMS_MODULE_NAME, 'rooms_module_activation_hook');

function rooms_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(ROOMS_MODULE_NAME, [ROOMS_MODULE_NAME]);

/**
 * Init rooms module menu items in setup in admin_init hook
 * @return null
 */
function rooms_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('rooms', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('rooms', [
            'name' => 'Rooms', // Directly using name for now, usually _l()
            'href' => admin_url('rooms'),
            'icon' => 'fa fa-bed',
            'position' => 30,
        ]);
    }
}
