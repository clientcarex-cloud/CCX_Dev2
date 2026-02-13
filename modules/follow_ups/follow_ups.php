<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Follow-ups
Description: Custom module for follow-ups management
Version: 1.0.0
Requires at least: 2.3.*
*/

define('FOLLOW_UPS_MODULE_NAME', 'follow_ups');

hooks()->add_action('admin_init', 'follow_ups_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(FOLLOW_UPS_MODULE_NAME, 'follow_ups_module_activation_hook');

function follow_ups_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FOLLOW_UPS_MODULE_NAME, [FOLLOW_UPS_MODULE_NAME]);

/**
 * Init follow_ups module menu items in setup in admin_init hook
 * @return null
 */
function follow_ups_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('follow_ups', [
        'name' => 'Follow-ups',
        'href' => admin_url('follow_ups'),
        'position' => 10,
        'icon' => 'fa fa-history',
    ]);
}
