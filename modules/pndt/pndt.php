<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: PNDT
Description: Module for PNDT.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('PNDT_MODULE_NAME', 'pndt');

hooks()->add_action('admin_init', 'pndt_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(PNDT_MODULE_NAME, 'pndt_module_activation_hook');

function pndt_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PNDT_MODULE_NAME, [PNDT_MODULE_NAME]);

/**
 * Init pndt module menu items in setup in admin_init hook
 * @return null
 */
function pndt_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('pndt', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('pndt', [
            'name' => 'PNDT', // Directly using name for now, usually _l()
            'href' => admin_url('pndt'),
            'icon' => 'fa fa-baby',
            'position' => 30,
        ]);
    }
}
