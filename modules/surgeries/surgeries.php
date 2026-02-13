<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Surgeries
Description: Module for Surgeries.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('SURGERIES_MODULE_NAME', 'surgeries');

hooks()->add_action('admin_init', 'surgeries_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(SURGERIES_MODULE_NAME, 'surgeries_module_activation_hook');

function surgeries_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(SURGERIES_MODULE_NAME, [SURGERIES_MODULE_NAME]);

/**
 * Init surgeries module menu items in setup in admin_init hook
 * @return null
 */
function surgeries_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('surgeries', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('surgeries', [
            'name' => 'Surgeries', // Directly using name for now, usually _l()
            'href' => admin_url('surgeries'),
            'icon' => 'fa fa-heartbeat',
            'position' => 30,
        ]);
    }
}
