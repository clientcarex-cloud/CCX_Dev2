<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: CCX Leads
Description: CCX Leads Management Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('CCX_LEADS_MODULE_NAME', 'ccx_leads');

hooks()->add_action('admin_init', 'ccx_leads_module_init_menu_items');
hooks()->add_action('admin_init', 'ccx_leads_permissions');

/**
 * Register activation module hook
 */
register_activation_hook(CCX_LEADS_MODULE_NAME, 'ccx_leads_module_activation_hook');

function ccx_leads_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(CCX_LEADS_MODULE_NAME, [CCX_LEADS_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function ccx_leads_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('ccx_leads', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('ccx_leads', [
            'name'     => 'CCX Leads',
            'href'     => admin_url('ccx_leads'),
            'position' => 30,
            'icon'     => 'fa fa-address-card',
        ]);
    }
}

function ccx_leads_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('ccx_leads', $capabilities, 'CCX Leads');
}
