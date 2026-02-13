<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Dr. Authorization
Description: Module for Dr. Authorization.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('DR_AUTHORIZATION_MODULE_NAME', 'dr_authorization');

hooks()->add_action('admin_init', 'dr_authorization_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(DR_AUTHORIZATION_MODULE_NAME, 'dr_authorization_module_activation_hook');

function dr_authorization_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(DR_AUTHORIZATION_MODULE_NAME, [DR_AUTHORIZATION_MODULE_NAME]);

/**
 * Init dr_authorization module menu items in setup in admin_init hook
 * @return null
 */
function dr_authorization_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('dr_authorization', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('dr_authorization', [
            'name' => 'Dr. Authorization', // Directly using name for now, usually _l()
            'href' => admin_url('dr_authorization'),
            'icon' => 'fa fa-user-md',
            'position' => 30,
        ]);
    }
}
