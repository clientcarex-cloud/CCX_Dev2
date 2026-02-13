<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Referral Lab
Description: Module for Referral Lab.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('B2B_LABS_MODULE_NAME', 'b2b_labs');

hooks()->add_action('admin_init', 'b2b_labs_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(B2B_LABS_MODULE_NAME, 'b2b_labs_module_activation_hook');

function b2b_labs_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(B2B_LABS_MODULE_NAME, [B2B_LABS_MODULE_NAME]);

/**
 * Init b2b_labs module menu items in setup in admin_init hook
 * @return null
 */
function b2b_labs_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('b2b_labs', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('b2b_labs', [
            'name' => 'Referral Lab', // Directly using name for now, usually _l()
            'href' => admin_url('b2b_labs'),
            'icon' => 'fa fa-building',
            'position' => 30,
        ]);
    }
}
