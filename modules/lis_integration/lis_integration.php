<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: LIS Integration
Description: Module for LIS Integration.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('LIS_INTEGRATION_MODULE_NAME', 'lis_integration');

hooks()->add_action('admin_init', 'lis_integration_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(LIS_INTEGRATION_MODULE_NAME, 'lis_integration_module_activation_hook');

function lis_integration_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(LIS_INTEGRATION_MODULE_NAME, [LIS_INTEGRATION_MODULE_NAME]);

/**
 * Init lis_integration module menu items in setup in admin_init hook
 * @return null
 */
function lis_integration_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('lis_integration', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('lis_integration', [
            'name' => 'LIS Integration', // Directly using name for now, usually _l()
            'href' => admin_url('lis_integration'),
            'icon' => 'fa fa-flask',
            'position' => 30,
        ]);
    }
}
