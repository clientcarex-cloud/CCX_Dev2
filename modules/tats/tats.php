<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: TATs
Description: Module for TATs.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TATS_MODULE_NAME', 'tats');

hooks()->add_action('admin_init', 'tats_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(TATS_MODULE_NAME, 'tats_module_activation_hook');

function tats_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(TATS_MODULE_NAME, [TATS_MODULE_NAME]);

/**
 * Init tats module menu items in setup in admin_init hook
 * @return null
 */
function tats_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('tats', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('tats', [
            'name' => 'TATs', // Directly using name for now, usually _l()
            'href' => admin_url('tats'),
            'icon' => 'fa fa-clock',
            'position' => 30,
        ]);
    }
}
