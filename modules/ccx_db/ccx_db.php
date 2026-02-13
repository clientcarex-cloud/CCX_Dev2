<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: CCX DB
Description: Database Management and Comparison Tool
Version: 1.0.0
Requires at least: 2.3.*
*/

define('CCX_DB_MODULE_NAME', 'ccx_db');

$CI = &get_instance();

/**
 * Register activation module hook
 */
register_activation_hook(CCX_DB_MODULE_NAME, 'ccx_db_activation_hook');

function ccx_db_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(CCX_DB_MODULE_NAME, [CCX_DB_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 */
hooks()->add_action('admin_init', 'ccx_db_init_menu_items');

function ccx_db_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('settings', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('ccx-db-options', [
            'name' => 'CCX DB',
            'href' => admin_url('ccx_db'),
            'position' => 65,
        ]);
    }
}
