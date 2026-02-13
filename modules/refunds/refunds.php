<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Refunds & Cancellations
Description: Manage patient refunds and cancellations.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('REFUNDS_MODULE_NAME', 'refunds');

hooks()->add_action('admin_init', 'refunds_module_init_menu_items');
hooks()->add_action('app_admin_head', 'refunds_add_head_components');

/**
 * Register activation module hook
 */
register_activation_hook(REFUNDS_MODULE_NAME, 'refunds_module_activation_hook');

function refunds_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(REFUNDS_MODULE_NAME, [REFUNDS_MODULE_NAME]);

/**
 * Init refunds module menu items in setup in admin_init hook
 * @return null
 */
function refunds_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('refunds', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('refunds', [
            'name' => _l('refunds'),
            'href' => admin_url('refunds'),
            'icon' => 'fa fa-undo',
            'position' => 30,
        ]);
    }
}

function refunds_add_head_components()
{
    // Add any necessary CSS/JS here
}
