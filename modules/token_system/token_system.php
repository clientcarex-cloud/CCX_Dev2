<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Token System
Description: Module for manual token generation, display, and queue management.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TOKEN_SYSTEM_MODULE_NAME', 'token_system');

hooks()->add_action('admin_init', 'token_system_module_init_menu_items');
hooks()->add_action('admin_init', 'token_system_permissions');

/**
 * Register activation module hook
 */
register_activation_hook(TOKEN_SYSTEM_MODULE_NAME, 'token_system_module_activation_hook');

function token_system_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(TOKEN_SYSTEM_MODULE_NAME, [TOKEN_SYSTEM_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function token_system_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('token_system', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('token_system', [
            'name' => _l('token_system'),
            'href' => admin_url('token_system'),
            'position' => 30,
            'icon' => 'fa fa-ticket',
        ]);

        $CI->app_menu->add_sidebar_children_item('token_system', [
            'slug' => 'token_system_queue',
            'name' => _l('todays_queue'),
            'href' => admin_url('token_system'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('token_system', [
            'slug' => 'token_system_displays',
            'name' => _l('display_management'),
            'href' => admin_url('token_system/token_displays'),
            'position' => 10,
        ]);

        $CI->app_menu->add_sidebar_children_item('token_system', [
            'slug' => 'token_system_settings',
            'name' => _l('settings'),
            'href' => admin_url('token_system/token_settings'),
            'position' => 15,
        ]);
    }
}

/**
 * Register module permissions
 */
function token_system_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('token_system', $capabilities, 'Token System');
}
