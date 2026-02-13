<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Print Templates
Description: Manage print templates with TinyMCE support.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PRINT_TEMPLATES_MODULE_NAME', 'print_templates');

hooks()->add_action('admin_init', 'print_templates_module_init_menu_items');
hooks()->add_action('admin_init', 'print_templates_permissions');

/**
 * Register activation module hook
 */
register_activation_hook(PRINT_TEMPLATES_MODULE_NAME, 'print_templates_module_activation_hook');

function print_templates_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PRINT_TEMPLATES_MODULE_NAME, [PRINT_TEMPLATES_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function print_templates_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('print_templates', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('print_templates', [
            'name' => _l('print_templates'),
            'href' => admin_url('print_templates'),
            'icon' => 'fa fa-print',
            'position' => 30,
        ]);
    }
}

/**
 * Init module permissions in admin_init hook
 */
function print_templates_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('print_templates', $capabilities, _l('print_templates'));
}
