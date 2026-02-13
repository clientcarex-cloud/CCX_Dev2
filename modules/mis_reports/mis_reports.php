<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: MIS Reports
Description: MIS Reports Module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MIS_REPORTS_MODULE_NAME', 'mis_reports');

hooks()->add_action('admin_init', 'mis_reports_module_init_menu_items');

/**
 * Register activation module hook
 */
register_activation_hook(MIS_REPORTS_MODULE_NAME, 'mis_reports_module_activation_hook');

function mis_reports_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function mis_reports_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('mis_reports', [
        'name' => 'MIS Reports',
        'href' => admin_url('mis_reports'),
        'position' => 15,
        'icon' => 'fa fa-area-chart',
    ]);
}
