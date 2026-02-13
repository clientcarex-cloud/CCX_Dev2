<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Shift Report
Description: Shift Report Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('SHIFT_REPORT_MODULE_NAME', 'shift_report');

// hooks()->add_action('admin_init', 'shift_report_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(SHIFT_REPORT_MODULE_NAME, 'shift_report_module_activation_hook');

function shift_report_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function shift_report_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('shift_report', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('shift_report', [
            'name' => 'Shift Report',
            'href' => admin_url('shift_report'),
            'position' => 10,
            'icon' => 'fa fa-file-text-o',
        ]);
    }
}

/**
 * Register module model
 */
hooks()->add_action('admin_init', 'shift_report_module_register_models');

function shift_report_module_register_models()
{
    $CI = &get_instance();
    $CI->load->model('shift_report/shift_report_model');
}
