<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Core CRM
Description: Hides default Perfex CRM menus (Estimates, Contracts, Projects, Sales, KB, Utilities, Setup)
Version: 1.0.0
Requires at least: 2.3.*
*/

define('CORE_CRM_MODULE_NAME', 'core_crm');

hooks()->add_filter('sidebar_menu_items', 'core_crm_hide_menus');
hooks()->add_action('app_admin_head', 'core_crm_hide_setup_css');

function core_crm_hide_menus($items)
{
    // List of menu slugs to control
    $menus_to_hide = [
        'estimate_request',
        'contracts',
        'projects',
        'sales',
        'knowledge-base', // Fixed slug
        'utilities',
        // 'setup', // Setup is handled via CSS
        'subscriptions'
    ];

    foreach ($menus_to_hide as $slug) {
        if (get_option('core_crm_hide_' . $slug) == '1') {
            foreach ($items as $key => $item) {
                if ($item['slug'] == $slug) {
                    unset($items[$key]);
                }
            }
        }
    }

    // Also check for 'setup' key in array just in case, but usually hardcoded
    /*
    if (get_option('core_crm_hide_setup') == '1') {
         // It's not in the array usually, handled by CSS
    }
    */

    return $items;
}

function core_crm_hide_setup_css()
{
    if (get_option('core_crm_hide_setup') == '1') {
        echo '<style>#setup-menu-item { display: none !important; }</style>';
    }
}
