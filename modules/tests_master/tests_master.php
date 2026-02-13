<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Tests Master
Description: Manage Lab Tests as Items with Code and Department
Version: 1.0.0
Requires at least: 2.3.*
*/

define('TESTS_MASTER_MODULE_NAME', 'tests_master');

hooks()->add_action('admin_init', 'tests_master_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(TESTS_MASTER_MODULE_NAME, 'tests_master_activation_hook');

function tests_master_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(TESTS_MASTER_MODULE_NAME, [TESTS_MASTER_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function tests_master_init_menu_items()
{
    $CI = &get_instance();

    // Check for DB updates on every admin load (lightweight check inside install.php)
    require_once(__DIR__ . '/install.php');

    if (has_permission('items', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('tests-master', [
            'name' => 'Tests Master',
            'href' => admin_url('tests_master'),
            'icon' => 'fa fa-flask',
            'position' => 35,
        ]);
    }
}
