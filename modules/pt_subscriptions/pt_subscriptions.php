<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Pt. Subscription
Description: Patient Subscription Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PT_SUBSCRIPTIONS_MODULE_NAME', 'pt_subscriptions');

hooks()->add_action('admin_init', 'pt_subscriptions_init_menu_items');

function pt_subscriptions_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('pt_subscriptions', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('pt_subscriptions', [
            'name' => 'Pt. Subscriptions', // The name if the item
            'href' => admin_url('pt_subscriptions'), // URL of the item
            'position' => 30, // The menu position
            'icon' => 'fa fa-refresh', // Font awesome icon
        ]);
    }
}

hooks()->add_action('admin_init', 'pt_subscriptions_permissions');

function pt_subscriptions_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('pt_subscriptions', $capabilities, _l('pt_subscriptions'));
}
