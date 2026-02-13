<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Insurance
Description: Module for Insurance.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('INSURANCE_MODULE_NAME', 'insurance');

hooks()->add_action('admin_init', 'insurance_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(INSURANCE_MODULE_NAME, 'insurance_module_activation_hook');

function insurance_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(INSURANCE_MODULE_NAME, [INSURANCE_MODULE_NAME]);

/**
 * Init insurance module menu items in setup in admin_init hook
 * @return null
 */
function insurance_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('insurance', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('insurance', [
            'name' => 'Insurance', // Directly using name for now, usually _l()
            'href' => admin_url('insurance'),
            'icon' => 'fa fa-shield',
            'position' => 30,
        ]);
    }
}
